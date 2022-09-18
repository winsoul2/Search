<?php
header('Content-Type: text/html');
?>
<style>
    #pdf-main-container {
        width: 800px;
        margin: 20px auto;
    }

    #pdf-loader {
        display: none;
        text-align: center;
        color: #999999;
        font-size: 13px;
        line-height: 100px;
        height: 100px;
    }

    #pdf-contents {
        display: none;
    }

    #pdf-meta {
        overflow: hidden;
        margin: 0 0 20px 0;
    }

    #pdf-buttons {
        float: left;
    }

    #page-count-container {
        float: right;
    }

    #pdf-current-page {
        display: inline;
    }

    #pdf-total-pages {
        display: inline;
    }

    #pdf-canvas {
        border: 1px solid rgba(0,0,0,0.2);
        box-sizing: border-box;
    }

    #page-loader {
        height: 100px;
        line-height: 100px;
        text-align: center;
        display: none;
        color: #999999;
        font-size: 13px;
    }

    #download-image {
        display: none;
        width: 150px;
        margin: 20px auto 0 auto;
        font-size: 13px;
        text-align: center;
    }
</style>
<div id="pdf-main-container">
    <div id="pdf-loader">Loading document ...</div>
    <div id="pdf-contents">
<!--        <div id="pdf-meta">-->
<!--            <div id="pdf-buttons">-->
<!--                <button id="pdf-prev">Previous</button>-->
<!--                <button id="pdf-next">Next</button>-->
<!--            </div>-->
<!--            <div id="page-count-container">Page <div id="pdf-current-page"></div> of <div id="pdf-total-pages"></div></div>-->
<!--        </div>-->
        <canvas id="pdf-canvas" width="400"></canvas>
        <div id="page-loader">Loading page ...</div>
        <a id="download-image" href="#">Download PNG</a>
    </div>
</div>
<script src="<?php echo base_url(PROJECTJSPATH."/assets/js")?>/jquery-1.10.2.min.js"></script>
<script src="<?php echo base_url(PROJECTJSPATH."/assets/js")?>/pdf.js"></script>
<script src="<?php echo base_url(PROJECTJSPATH."/assets/js")?>/pdf.worker.js"></script>
<script defer>

    var __PDF_DOC,
        __CURRENT_PAGE,
        __TOTAL_PAGES,
        _SCALE = 2,
        __PAGE_RENDERING_IN_PROGRESS = 0,
        __CANVAS = $('#pdf-canvas').get(0),
        __CANVAS_CTX = __CANVAS.getContext('2d');

    function showPDF() {
        $("#pdf-loader").show();
        var pdf_url = '<?php echo $_target;?>';

        PDFJS.getDocument({ url: pdf_url }).then(function(pdf_doc) {
            __PDF_DOC = pdf_doc;
            __TOTAL_PAGES = __PDF_DOC.numPages;

            // Hide the pdf loader and show pdf container in HTML
            $("#pdf-loader").hide();
            $("#pdf-contents").show();
            $("#pdf-total-pages").text(__TOTAL_PAGES);

            // Show the first page
            showPage(1);
        }).catch(function(error) {
            // If error re-show the upload button
            $("#pdf-loader").hide();
            $("#upload-button").show();

            alert(error.message);
        });
    }

    function showPage(page_no) {
        __PAGE_RENDERING_IN_PROGRESS = 1;
        __CURRENT_PAGE = page_no;

        // Disable Prev & Next buttons while page is being loaded
        $("#pdf-next, #pdf-prev").attr('disabled', 'disabled');

        // While page is being rendered hide the canvas and show a loading message
        $("#pdf-canvas").hide();
        $("#page-loader").show();
        $("#download-image").hide();

        // Update current page in HTML
        $("#pdf-current-page").text(page_no);

        // Fetch the page
        __PDF_DOC.getPage(page_no).then(function(page) {
            // As the canvas is of a fixed width we need to set the scale of the viewport accordingly
            //var scale_required = (__CANVAS.width ) / page.getViewport(2).width;
            //var scale_required = _SCALE;

            // Get viewport of the page at required scale
            var viewport = page.getViewport(_SCALE);

            // Set canvas height
            __CANVAS.height = viewport.height;
            __CANVAS.width = viewport.width;

            __CANVAS.height = viewport.height;
            __CANVAS.width = viewport.width;

            var renderContext = {
                canvasContext: __CANVAS_CTX,
                viewport: viewport
            };

            // Render the page contents in the canvas
            page.render(renderContext).then(function() {
                __PAGE_RENDERING_IN_PROGRESS = 0;

                // Re-enable Prev & Next buttons
                $("#pdf-next, #pdf-prev").removeAttr('disabled');

                // Show the canvas and hide the page loader
                $("#pdf-canvas").show();
                $("#page-loader").hide();
                //$("#download-image").show();
            });
        });
    }

    // Upon click this should should trigger click on the #file-to-upload file input element
    // This is better than showing the not-good-looking file input element
    $("#upload-button").on('click', function() {
        $("#file-to-upload").trigger('click');
    });

    // When user chooses a PDF file
    $("#file-to-upload").on('change', function() {
        // Validate whether PDF
        if(['application/pdf'].indexOf($("#file-to-upload").get(0).files[0].type) == -1) {
            alert('Error : Not a PDF');
            return;
        }

        $("#upload-button").hide();

        // Send the object url of the pdf
        showPDF(URL.createObjectURL($("#file-to-upload").get(0).files[0]));
    });

    // Previous page of the PDF
    $("#pdf-prev").on('click', function() {
        if(__CURRENT_PAGE != 1)
            showPage(--__CURRENT_PAGE);
    });

    // Next page of the PDF
    $("#pdf-next").on('click', function() {
        if(__CURRENT_PAGE != __TOTAL_PAGES)
            showPage(++__CURRENT_PAGE);
    });

    // Download button
    $("#download-image").on('click', function() {
        $(this).attr('href', __CANVAS.toDataURL()).attr('download', 'receipt_<?php echo @$receipt_id; ?>.png');

    });

    $(document).ready(function(){
        showPDF();
//        setTimeout(function(){
//            ///downloadCanvas();
//            var data = {};
//            var header = new Headers();
//            header.set('Content-Type','application/json');
//            header.set('Charset','utf-8');
//            data['data'] = __CANVAS.toDataURL();
//            window.document.write(JSON.stringify(data));
//        }, 100);
    });

    function downloadImage(){
        document.getElementById('download-image').click();
    }

    function downloadCanvas(){
        var a = document.getElementById('download-image');
        var b = a.href;
        a.href =  __CANVAS.toDataURL();
        downloadImage();
        a.href = b;
    }


</script>