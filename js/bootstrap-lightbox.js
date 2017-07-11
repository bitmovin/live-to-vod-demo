/**
 * Howto:
 * 0) include this script into your demo-page:
 * <script type="text/javascript" src="../shared/js/bootstrap-lightbox.js"></script>
 * 1) Add css class "bitmovin-lightbox" to the html element you want to use to load the image
 * 2) Add the data-attribute "data-img-url" with the URL to the image as its value
 * 3) Click the element - have fun!
 */

$(document).ready(function () {
    $('body').append('<div tabindex="-1" class="modal fade" id="bitmovin-modal" role="dialog"><div class="modal-dialog bitmovin-modal-lg"><div class="modal-content"><div class="modal-body text-center"></div><div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>');
    $(".bitmovin-lightbox").click(function () {
        var imgUrl = this.dataset.imgUrl || false;
        //var customModalId = this.dataset.modalId || false;
        var modalId = this.dataset.modalId || 'bitmovin-modal';

        console.log("img-url", imgUrl);
        console.log("selected modal", modalId);

        if(!this.dataset.hasOwnProperty('modalId')) {
            var oImg = document.createElement('img');
            oImg.setAttribute('src', imgUrl);
            oImg.setAttribute('class', 'img-responsive bitmovin-center-element');
            $('.modal-body').empty();
            $(oImg).appendTo('.modal-body');
        }

        $('#' + modalId).modal({show: true});
    });
});
