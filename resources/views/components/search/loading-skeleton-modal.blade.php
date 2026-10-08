{{-- 100% Exact Downloaded Agoda Loading Screen & Modal (From Agoda Official Source) --}}
<style type="text/css">
    #ModalLoadingSpinner {
        background: rgba(0, 0, 0, 0.4);
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 999999;
        display: none;
    }

    .ModalLoadingSpinner__content {
        padding: 32px;
        text-align: center;
        height: 204px;
        width: 427px;
        max-width: 90vw;
        background: white;
        border-radius: 6px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .ModalLoadingSpinner__content--icon {
        position: relative;
        height: 68px;
        width: 100%;
        margin-bottom: 8px;
        display: inline-block;
        overflow: hidden;
    }

    .ModalLoadingSpinner__content .content-img {
        position: absolute;
        top: 0;
        left: 50%;
        margin: 0;
        transform: translateX(-50%);
        display: inline-block;
        height: 68px;
        width: 68px;
        opacity: 1;
        object-fit: contain;
    }

    .ModalLoadingSpinner__content--text {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-weight: 400;
        line-height: 1.42857;
    }

    .content-info-header {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        color: #8e6acb;
    }

    .content-info-detail {
        font-size: 13px;
        color: #555555;
        margin: 0;
        margin-top: 5px;
    }
</style>

<div id="ModalLoadingSpinner">
    <div class="ModalLoadingSpinner__content">
        <div class="ModalLoadingSpinner__content--icon">
            <img class="content-img" src="{{ asset('images/agoda/agoji-parachute.gif') }}" alt="Agoda Mascot" />
        </div>
        <div class="ModalLoadingSpinner__content--text">
            <h3 class="content-info-header">Just a moment!</h3>
            <p class="content-info-detail">We're finding great stays for your dates and destination.</p>
        </div>
    </div>
</div>

<script>
    window.showAgodaSearchLoading = function() {
        var modal = document.getElementById('ModalLoadingSpinner');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    window.hideAgodaSearchLoading = function() {
        var modal = document.getElementById('ModalLoadingSpinner');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    window.addEventListener('pageshow', function() {
        window.hideAgodaSearchLoading();
    });
</script>
