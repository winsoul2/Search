CKEDITOR.plugins.add('linkembed',
{
		init: function (editor) {
				var pluginName = 'linkembed';
				editor.ui.addButton('linkembed',
						{
								label: 'Insert Embed',
								command: 'EmbedDialog',
								icon: CKEDITOR.plugins.getPath('linkembed') + 'img.png'
						});
				var cmd = editor.addCommand('EmbedDialog', { exec: showDialogEmbed });
		}
});

function showDialogEmbed(editor) {
	$.fancybox({
		'href' : '/scripts/ckeditor/embed.php'
		, padding : 10
		, width : 750
		, height : 350
		, modal : false
		, type : 'iframe'
		, autoSize	: false
		, openEffect : 'none'
		, closeEffect : 'none'
		, closeBtn : true
		, title : "แทรก Embed Code"
		, afterClose : function() {
			editor.insertHtml($.cookies.get("return_embed"));
			$.cookies.del("return_embed")
		}
	});
}
