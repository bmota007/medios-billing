/<\/div>\s*<\/div>\s*<\/div>/i\
@if(!empty($quote->customer_notes))\
<div class="mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">\
<h3 class="text-lg font-bold text-slate-900 mb-3">Client Notes</h3>\
<p class="text-slate-600 leading-7">\
{!! nl2br(e($quote->customer_notes)) !!}\
</p>\
</div>\
@endif
