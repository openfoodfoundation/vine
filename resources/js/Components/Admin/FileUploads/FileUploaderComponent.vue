<template>
    <div>
        <div v-bind="getRootProps()">
            <input v-bind="getInputProps()"/>
            <div class="border bg-gray-100 rounded p-8 text-center cursor-pointer hover:border-2 hover:border-dashed">
                <p v-if="isDragActive">Drop the file here ...</p>
                <p v-else>Drag 'n' drop a files here, or click to select files</p>
            </div>
        </div>
    </div>
</template>

<script>
import {useDropzone} from "vue3-dropzone";
import Swal from "sweetalert2";

export default {
    props: {
        folder: {
            type: String,
            required: true
        },
        visibility: {
            type: String,
            default: null
        },
        allowedFileTypes: String,
        uploadSuccessFunction: Function,
    },
    setup(props, context) {

        const saveFiles = (files) => {
            let payload = {
                // folder: '/leaflets/images',
                folder: props.folder,
                files: [],
                visibility: 'public',
            };

            const formData = new FormData(); // pass data as a form
            for (var x = 0; x < files.length; x++) {
                // append files as array to the form, feel free to change the array name
                formData.append("files[]", files[x]);
                payload.files.push(files[x]);
            }

            formData.append("folder", props.folder);
            formData.append("returnFilePathOnly", props.folder);
            formData.append("acceptedFileTypes", props.allowedFileTypes);

            axios.post('/admin/file-uploads', formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }).then(response => {

                if (response.data.data) {
                    context.emit('filesWereUploaded', response.data.data)
                }

            }).catch(function (error) {
                console.log(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: error.response.data.meta.message
                });
            });
        };

        function onDrop(acceptFiles, rejectReasons) {

            /**
             * Handle error messages
             */
            if (rejectReasons[0]?.errors[0]?.message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: rejectReasons[0]?.errors[0]?.message
                });
            } else {
                saveFiles(acceptFiles);
            }
        }

        let dropzoneOptions = {
            onDrop,
            maxFiles: 1,
            accept: props.allowedFileTypes,
            multiple: 0
        };

        const {getRootProps, getInputProps, ...rest} = useDropzone(dropzoneOptions);

        return {
            getRootProps,
            getInputProps,
            ...rest,
        };
    },

};
</script>

