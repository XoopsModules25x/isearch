<table class="width100 bnone">
<{foreach item=onesearch from=$block.biggesusers}>
    <tr class="<{cycle values="even,odd"}>"><td class="center"><{$onesearch.uname}></td><td class="center"><{$onesearch.count}></td></tr>
<{/foreach}>
</table>
