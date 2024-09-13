<x-mail::message>

    #

    Hello.
    A Vine voucher set has been generated for your service.
    Voucher set #{{$voucherSetId}}
    Created by {{$createdBy}}

    Please select one of the options below by clicking the corresponding button.

<x-mail::button :url="$approve">
    Approve
</x-mail::button>

<x-mail::button :url="$reject">
    Reject
</x-mail::button>

    This link will be available for the next 7 days.
    Thank you for using our platform!

</x-mail::message>