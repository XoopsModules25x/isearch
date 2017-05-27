<{if $visiblekeywords>0}>
<h2><{$smarty.const._ISEARCH_KEYWORD}></h2>
<br>
<{if $pagenav}><div class="left marg10"><{$smarty.const._ISEARCH_PAGE}> <{$pagenav}></div><{/if}>
<table class="width100 bnone">
    <tr><th class="center"><{$smarty.const._ISEARCH_DATE}></th><th class="center"><{$smarty.const._ISEARCH_KEYWORD}></th></tr>
<{foreach item=onekeyword from=$keywords}>
    <tr class="<{cycle values="even,odd"}>"><td class="center"><{$onekeyword.date}></td><td class="center"><{$onekeyword.keyword}></td></tr>
<{/foreach}>
</table>
<{if $pagenav}><div class="left marg10"><{$smarty.const._ISEARCH_PAGE}> <{$pagenav}></div><{/if}>
<{/if}>
