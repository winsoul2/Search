        <?php
        $link = array(
            'src' => PROJECTJSPATH.'assets/js/vendor.min.js',
            'language' => 'javascript',
            'type' => 'text/javascript'
        );
        echo script_tag($link);
        $link = array(
            'src' => PROJECTJSPATH.'assets/js/elephant.min.js',
            'language' => 'javascript',
            'type' => 'text/javascript'
        );
        echo script_tag($link);
        ?>
<footer class="main-footer">
            <div class="pull-right hidden-xs">
                <strong></strong>
            </div>
            <div></div>
        </footer>
    </div><!-- ./wrapper -->
</body>
</html>
