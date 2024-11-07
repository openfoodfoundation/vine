<x-mail::message>

Hello!

A Vine voucher set is about to be generated that may be redeemed at your shop.


<div style="font-weight: bold">
   Your Shop
</div>
<div>
    {{$merchantTeam->name ?? '(Unknown team name)'}}
</div>
<br>
<div style="font-weight: bold">
    Voucher Set
</div>
<div>
    #{{$voucherSetId}}
</div>
<br>

<div style="font-weight: bold">
    Total Value of Voucher Set
</div>
<div>
    {{ ($voucherSet->total_set_value / 100) }} {{$voucherSet->currencyCountry?->currency_code}}
</div>
<div style="font-size: 10px;font-style: italic">
    This is the total amount that may be redeemed through voucher redemptions.
    Individual vouchers can vary in value.
</div>

<br>
<div style="font-weight: bold">
    Service Team
</div>
<div>
    {{$voucherSet->allocatedToServiceTeam?->name}}
</div>

@if(isset($voucherSet->fundedByTeam?->nam))
<br>
<div style="font-weight: bold">
    Funding Team
</div>
<div>
    {{$voucherSet->fundedByTeam?->name}}
</div>
@endif

<br>
<div style="font-weight: bold">
    Created By
</div>
<div>
    {{$createdBy}}
</div>

<br>
<br>
Please select one of the options below by clicking the corresponding button.

<table style="width: 100%">
<tr>
<td style="width: 50%">
<x-mail::button :url="$approve" color="primary">
Approve - Looks Good!
</x-mail::button>
</td>
<td style="width: 50%">
<x-mail::button :url="$reject" color="error">
Reject - Not For Us
</x-mail::button>
</td>
</tr>
</table>

<div>
    These options will be available for the next 2 days.
    Thank you for using our platform!
</div>

</x-mail::message>
