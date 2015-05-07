    <footer class="main">

      <strong>&copy 2015</strong> Hammer Leilões - Área Administrativa by <a href="#" target="_blank">Kraft Consulting</a>

    </footer>

</div> <!-- main-content -->



<?php
    $environment = new ElementosRoot;

    # Elementos Css Bottom

    $environment->get_css("jquery-jvectormap-1.2.2","includes/assets/js/jvectormap/");
    $environment->get_css("rickshaw.min","includes/assets/js/rickshaw/");
    $environment->get_css("datatables.responsive","includes/assets/js/datatables/responsive/css/");
    $environment->get_css("select2-bootstrap","includes/assets/js/select2/");
    $environment->get_css("select2","includes/assets/js/select2/");

    # Elementos Javascript Bottom

    $environment->get_jsc("main-gsap","includes/assets/js/gsap/");
    $environment->get_jsc("jquery-ui-1.10.3.minimal.min","includes/assets/js/jquery-ui/js/");
    $environment->get_jsc("jquery.fancybox", "includes/assets/js/fancybox/");
    $environment->get_jsc("bootstrap");
    $environment->get_jsc("joinable");
    $environment->get_jsc("resizeable");
    $environment->get_jsc("neon-api");
    $environment->get_jsc("jquery.knob");
    $environment->get_jsc("jquery.dataTables.min");
    $environment->get_jsc("TableTools.min","includes/assets/js/datatables/");
    $environment->get_jsc("dataTables.bootstrap");
    $environment->get_jsc("jquery.dataTables.columnFilter","includes/assets/js/datatables/");
    $environment->get_jsc("lodash.min","includes/assets/js/datatables/");
    $environment->get_jsc("datatables.responsive","includes/assets/js/datatables/responsive/js/");
    $environment->get_jsc("select2.min","includes/assets/js/select2/");
    $environment->get_jsc("jquery-jvectormap-1.2.2.min","includes/assets/js/jvectormap/");
    $environment->get_jsc("jquery-jvectormap-europe-merc-en","includes/assets/js/jvectormap/");
    $environment->get_jsc("jquery.sparkline.min");
    $environment->get_jsc("d3.v3","includes/assets/js/rickshaw/vendor/");
    $environment->get_jsc("rickshaw.min","includes/assets/js/rickshaw/");
    $environment->get_jsc("raphael-min");
    $environment->get_jsc("morris.min");
    $environment->get_jsc("bootstrap-switch.min");
    $environment->get_jsc("neon-chat");
    $environment->get_jsc("jquery.inputmask");
    $environment->get_jsc("jquery.inputmask.bundle.min");
    $environment->get_jsc("jsapi");/* google api*/
    $environment->get_jsc("neon-custom");
    $environment->get_jsc("neon-demo");
    $environment->get_jsc("toastr");
    $environment->get_jsc("fileinput");
    $environment->get_jsc("bootstrap-datepicker");
    $environment->get_jsc("franx_custom");
    $environment->get_jsc("jquery.validate.min");



    $environment->get_jsc("jquery.ui.widget","includes/assets/js/file-upload/vendor/");
    $environment->get_jsc("tmpl.min","includes/assets/js/file-upload/");

    $environment->get_jsc("load-image.all.min","includes/assets/js/file-upload/");

    //The Canvas to Blob plugin is included for image resizing functionality
    $environment->get_jsc("canvas-to-blob.min","includes/assets/js/file-upload/");

    $environment->get_jsc("jquery.blueimp-gallery.min","includes/assets/js/file-upload/");

    $environment->get_jsc("jquery.iframe-transport","includes/assets/js/file-upload/");

    $environment->get_jsc("jquery.fileupload","includes/assets/js/file-upload/");

    $environment->get_jsc("jquery.fileupload-process","includes/assets/js/file-upload/");
    $environment->get_jsc("jquery.fileupload-image","includes/assets/js/file-upload/");

    $environment->get_jsc("jquery.fileupload-validate","includes/assets/js/file-upload/");

    $environment->get_jsc("jquery.fileupload-ui","includes/assets/js/file-upload/");


?>

</div>


</body>
</html>