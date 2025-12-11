CKEDITOR.editorConfig = function(config) {
    config.allowedContent = true;
    config.extraAllowedContent = 'div(col-*-*,container,container-fluid,row,about-container)';
    CKEDITOR.dtd.$removeEmpty['i'] = false;
    CKEDITOR.dtd.$removeEmpty['span'] = false;
    config.resize_enabled = false;

    config.contentsCss = [
        CKEDITOR.getUrl('contents.css'),
        '/assets/css/fonts.css'   // absolute path
    ];
        
        config.font_names =
            "Kalpurush ANSI/Kalpurush ANSI;" +
            'Adorsho Lipi/Adorsho Lipi;' +
            'SolaimanLipi/SolaimanLipi;' +
            'SutonnyMJ/SutonnyMJ;' +
            'Siyam Rupali ANSI/Siyam Rupali ANSI;' +
            'Siyamrupali/Siyamrupali;' +
            'Arial/Arial, Helvetica, sans-serif;' +
            'Times New Roman/Times New Roman, Times, serif;' +
            'Courier New/Courier New, Courier, monospace;';

};
