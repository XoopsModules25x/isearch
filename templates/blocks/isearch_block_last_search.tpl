<{if $block.visiblekeywords>0}>
<table class="width100 bnone">
<{foreach item=onesearch from=$block.searches}>
    <tr class="<{cycle values="even,odd"}>"><td class="center"><{$onesearch.keyword}></td></tr>
<{/foreach}>
</table>
<{/if}>
