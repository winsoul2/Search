CKEDITOR.plugins.add('linkbutton',
{
		init: function (editor) {
				var pluginName = 'linkbutton';
				editor.ui.addButton('linkbutton',
						{
								label: 'Insert Image',
								command: 'ImageDialog',
								icon: CKEDITOR.plugins.getPath('linkbutton') + 'logo.png'
						});
				var cmd = editor.addCommand('ImageDialog', { exec: showDialog });
		}
});

function showDialog(editor) {
	$.fancybox({
		'href' : '/scripts/ckeditor/upload.php'
		, padding : 10
		, width : 500
		, height : 200
		, modal : false
		, type : 'iframe'
		, autoSize	: false
		, openEffect : 'none'
		, closeEffect : 'none'
		, closeBtn : true
		, title : "Upload รูปภาพ"
		, afterClose : function() {
			if($.cookies.get("return") != null) {
				editor.insertHtml("<img src='"+$.cookies.get("return")+"' alt='UploadImage' />");
			}
			$.cookies.del("return")
		}
	});
}
