CKEDITOR.plugins.add('linkyoutube',
{
		init: function (editor) {
				var pluginName = 'linkyoutube';
				editor.ui.addButton('linkyoutube',
						{
								label: 'Insert Youtube',
								command: 'YoutubeDialog',
								icon: CKEDITOR.plugins.getPath('linkyoutube') + 'img.gif'
						});
				var cmd = editor.addCommand('YoutubeDialog', { exec: showDialogYoutube });
		}
});

function showDialogYoutube(editor) {
	$.fancybox({
		'href' : '/scripts/ckeditor/youtube.php'
		, padding : 10
		, width : 750
		, height : 120
		, modal : false
		, type : 'iframe'
		, autoSize	: false
		, openEffect : 'none'
		, closeEffect : 'none'
		, closeBtn : true
		, title : "แทรก Youtube"
		, afterClose : function() {
			editor.insertHtml($.cookies.get("return_youtube"));
			$.cookies.del("return_youtube")
		}
	});
}
