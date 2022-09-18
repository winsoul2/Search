CKEDITOR.plugins.add('linkfilebutton',
{
		init: function (editor) {
				var pluginName = 'linkfilebutton';
				editor.ui.addButton('linkfilebutton',
						{
								label: 'Insert File',
								command: 'FileDialog',
								icon: CKEDITOR.plugins.getPath('linkfilebutton') + 'link.png'
						});
				var cmd = editor.addCommand('FileDialog', { exec: showDialogFile });
		}
});

function showDialogFile(editor) {
	$.fancybox({
		'href' : '/scripts/ckeditor/upload.file.php'
		, padding : 10
		, width : 500
		, height : 200
		, modal : false
		, type : 'iframe'
		, autoSize	: false
		, openEffect : 'none'
		, closeEffect : 'none'
		, closeBtn : true
		, title : "Upload ไฟล์"
		, afterClose : function() {
			if($.cookies.get("return") != null) {
				console.log($.cookies.get("return"));
				editor.insertHtml("<a href='"+$.cookies.get("return")+"' target='_blank'>ไฟล์แนบ</a>");
			}
			$.cookies.del("return")
		}
	});
}
