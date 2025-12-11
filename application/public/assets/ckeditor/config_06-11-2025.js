CKEDITOR.editorConfig = function(config) {
    // Allow all content
    config.allowedContent = true;

    // Extra allowed classes for Bootstrap/grid layout
    config.extraAllowedContent = 'div(col-*-*,container,container-fluid,row,about-container)';

    // Keep empty <i> and <span> tags
    CKEDITOR.dtd.$removeEmpty['i'] = false;
    CKEDITOR.dtd.$removeEmpty['span'] = false;

    // Toolbar configuration (basic features)
    /* config.toolbar = [
        { name: 'document', items: ['Source', '-', 'Preview', '-', 'Print'] },
        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
        { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
        { name: 'colors', items: ['TextColor', 'BGColor'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
        { name: 'insert', items: ['Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'] },
        { name: 'tools', items: ['Maximize'] }
    ]; */

    config.resize_enabled = false;

    // ---------------------------
    // Add Bangla fonts
    // ---------------------------
    config.font_names =
        'Soleman Lipi/Soleman Lipi;' +
        'SuttenyMJ/SuttenyMJ;' +
        'Kalpurush/Kalpurush;' +
        'Adorsho Lipi/Adorsho Lipi;' +
        'Arial/Arial, Helvetica, sans-serif;' +
        'Times New Roman/Times New Roman, Times, serif;' +
        'Courier New/Courier New, Courier, monospace;';

};
