(function(){
	//Section 1 : Code to execute when the toolbar button is pressed
	var linkbutton_exec= {
		exec:function(editor){
			var w=750;
			var h=300;
			var left = (screen.width/2)-(w/2);
			var top = (screen.height/2)-(h/2);
			var media = window.showModalDialog("/scripts/ckeditor/insert_embed.php",null,"dialogWidth:"+w+"px;dialogHeight:"+h+"px;center:yes; resizable: no; help: no;");  
			if(media!=null)
				editor.insertHtml(media);


		}
	},
	//Section 2 : Create the button and add the functionality to it
	b='insembed';
	CKEDITOR.plugins.add(b,{
		init:function(editor){
			editor.addCommand(b,linkbutton_exec);
			editor.ui.addButton('insembed',{
				label:'Insert Embed',
				icon: this.path + 'img.png',
				command:b
			});
		}
	});
})();
