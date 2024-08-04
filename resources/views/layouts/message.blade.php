<style>
    .alert {
        position: relative;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.375rem;
        opacity: 1;
        transition: opacity 0.5s ease, max-height 0.5s ease;
        max-height: 100px;
        /* Adjust based on your alert content height */
    }

    .alert.alert-hide {
        opacity: 0;
        max-height: 0;
        overflow: hidden;
    }

    .close {
        position: absolute;
        top: 0.75rem;
        right: 1rem;
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: 0.5;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .close:hover {
        color: #000;
        text-decoration: none;
        opacity: 0.75;
    }

</style>
@if(Session::has('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {{ Session::get('success') }}
</div>
@endif
@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {{ Session::get('error') }}
</div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('.alert .close').forEach(function(closeButton) {
            closeButton.addEventListener('click', function(e) {
                const alert = closeButton.closest('.alert');
                alert.classList.add('alert-hide');

                // Wait for the transition to end before fully removing the alert
                alert.addEventListener('transitionend', () => {
                    alert.style.display = 'none';
                }, {
                    once: true
                });
            });
        });
    });

</script>
