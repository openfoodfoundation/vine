import{G as r,r as i,e as d,l as c,x as p,o as s,c as o,t as m,z as f}from"./app-D7vdOgIt.js";const b={__name:"TextInput",props:{modelValue:{type:String,required:!0},modelModifiers:{}},emits:["update:modelValue"],setup(e,{expose:a}){const u=r(e,"modelValue"),t=i(null);return d(()=>{t.value.hasAttribute("autofocus")&&t.value.focus()}),a({focus:()=>t.value.focus()}),(x,l)=>c((s(),o("input",{class:"border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm","onUpdate:modelValue":l[0]||(l[0]=n=>u.value=n),ref_key:"input",ref:t},null,512)),[[p,u.value]])}},_={class:"block font-medium text-sm text-gray-700"},v={key:0},g={key:1},h={__name:"InputLabel",props:{value:{type:String}},setup(e){return(a,u)=>(s(),o("label",_,[e.value?(s(),o("span",v,m(e.value),1)):(s(),o("span",g,[f(a.$slots,"default")]))]))}};export{h as _,b as a};