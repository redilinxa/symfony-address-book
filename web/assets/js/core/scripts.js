function deleteAction(object, url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#9325a5',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            performAjax("POST", url, {object : object})
                .then(res => {
                    if(res.success){
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        );

                        $("#" + object).remove();
                    }
                });
        }
    })
}


function setFormValidation(id) {
    $(id).validate({
        highlight: function (element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
            $(element).closest('.form-check').removeClass('has-success').addClass('has-danger');
        },
        success: function (element) {
            $(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
            $(element).closest('.form-check').removeClass('has-danger').addClass('has-success');
        },
        errorPlacement: function (error, element) {
            $(element).closest('.form-group').append(error);
        },
    });
}

async function performAjax(method, url, data) {
    let promise = new Promise((resolve) => {
        $.ajax({
            method: method,
            url: url,
            data: data,
            success: function (response) {
                resolve(response);
            }
        })
    });
    return await promise;
}