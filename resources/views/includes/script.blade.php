\<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>


<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    $(document).ready(function() {
        $.ajax({
            url: '/',
            method: 'GET',
            success: function(response) {
                $('#total-barangs').text(response.totalBarangs);
            }
        });
    });
</script>
