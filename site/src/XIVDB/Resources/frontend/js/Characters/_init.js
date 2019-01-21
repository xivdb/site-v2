// Initialize form classes
var ProfileNavigation = new ProfileNavigationClass(),
    ManualUpdate = new ManualUpdateClass();

$(function() {
    // Watch navigation
    ProfileNavigation.watch();
    ManualUpdate.watch();

    $('button[data-remove-gearset]').on('click', event => {
        var url = $(event.currentTarget).attr('data-remove-gearset');
        $.ajax({
            url: url,
            cache: false,
            success: response => {
                if (response[0]) {
                    swal("All good!", response[1], "success");
                } else {
                    swal("Wups", response[1], "error");
                }
            },
            error: (a,b,c) => {

            }
        });
    });
});
