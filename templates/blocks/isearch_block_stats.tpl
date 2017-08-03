<table class="width100 bnone">
<{foreach item=onesearch from=$block.mostsearched}>
    <tr class="<{cycle values="even,odd"}>"><td class="center"><{$onesearch.keyword}></td><td class="center"><{$onesearch.count}></td></tr>
<{/foreach}>
</table>
