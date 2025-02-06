jQuery(document).ready(function ($) {
    $('.acf-import-link').click(function (e) {
        e.preventDefault();

        const postId = $(this).data('post-id');

        const modal = `
        <div id="acf-import-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px;">
                <h2>Import from excel file</h2>
                <form id="acf-import-form" enctype="multipart/form-data">
                    <input type="file" name="acf_excel_file" accept=".xlsx, .xls" required />
                    <input type="hidden" name="post_id" value="${postId}" />
                    <button type="submit">Import</button>
                    <button type="button" id="acf-import-cancel">Cancel</button>
                </form>
            </div>
        </div>`;

        $('body').append(modal);

        $('#acf-import-cancel').click(function () {
            $('#acf-import-modal').remove();
        });

        $('#acf-import-form').submit(function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'acf_import_excel');
            formData.append('nonce', acfExcelHandler.importNonce);

            $.ajax({
                url: acfExcelHandler.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    alert(response.data);
                    $('#acf-import-modal').remove();
                },
                error: function () {
                    alert('An error occurred while importing the file.');
                }
            });
        });
    });
});
