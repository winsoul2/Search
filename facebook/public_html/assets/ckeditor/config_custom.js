/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.toolbar = 'MyToolbar';
	config.toolbar_MyToolbar =
	[
		['FontSize'],	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
		['TextColor','BGColor','Table'],
		['Bold','Italic','Underline','StrikeThrough','-','OrderedList','UnorderedList'],
		'/',
		['Cut','Copy','Paste','PasteText','PasteWord','-','Undo','Redo'],
		['Link','Unlink','-','linkbutton','insembed']
		, ['Source']
	];
	
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	config.language = 'th';
	config.skin='moono';
	config.resize_enabled = false;

	//config.filebrowserBrowseUrl = '/scripts/ckeditor/upload.php';
	//config.filebrowserImageBrowseUrl = '/scripts/ckeditor/upload.php?type=Images';
};
CKEDITOR.on('dialogDefinition', function( ev )
{

	var dialogName = ev.data.name;  
	var dialogDefinition = ev.data.definition;
   
	switch (dialogName) {  
	case 'image': //Image Properties dialog      
		dialogDefinition.removeContents('advanced');
		break;      
	case 'link': //image Properties dialog          
		dialogDefinition.removeContents('advanced');   
		var infoTab = dialogDefinition.getContents( 'info' );
		infoTab.remove( 'linkType');
         infoTab.remove( 'protocol');

		break;
	}
});

