
jQuery(document).ready(function ($) {
   "use strict";

   const BgaeLightbox = {
      /**
       * Start handling
       */
      run() {
         this.addHandlers();
      },
      /**
       * Adds handlers
       */
      addHandlers() {
         //Save this
         let self = this;
         //TODO: add more specific handler point (See https://api.jquery.com/on/)
         //Handle click on a title in the lightbox popup
         $("body").on("click", '.elementor-slideshow__title', function (e) {
            self.processTitleClick(e, 'title');
         });
         $("body").on("click", '.elementor-slideshow__description', function (e) {
            self.processTitleClick(e, 'description');
         });
      },
      /**
       * A caption click handler
       * @param {Event} e An Event object
       * @param {string} captionType Type of caption: 'title' or 'description'
       * @returns 
       */
      processTitleClick(e, captionType) {
         //Find the lightbox widget
         let $lightBox = $(e.currentTarget).parents(".dialog-lightbox-widget");
         //Check a finding result
         if ($lightBox.length == 0) {
            return;
         }
         //Extract a lightbox widget id
         let lightBoxWidgetIdReMatches = /^elementor-lightbox-slideshow-(.*)$/.exec($lightBox.attr("id"));

         //Check a regexp result
         if (lightBoxWidgetIdReMatches == null || lightBoxWidgetIdReMatches.length != 2) {
            return;
         }
         let lightBoxWidgetId = lightBoxWidgetIdReMatches[1];
         //Find an active lightbox item
         let $lightBoxItem = $lightBox.find(".elementor-lightbox-item.swiper-slide-active");
         //Check a finding result
         if ($lightBoxItem.length == 0) {
            return;
         }
         //Retrieve an active element index
         let lightBoxItemIndexStr = $lightBoxItem.data('swiper-slide-index');
         if (lightBoxItemIndexStr.length == 0) {
            return;
         }
         let lightBoxItemIndex = parseInt(lightBoxItemIndexStr);
         if (isNaN(lightBoxItemIndexStr) || (lightBoxItemIndex < 0)) {
            return;
         }
         //Find a corresponding gallery block
         let $gallery = $(".elementor-widget-band-gallery-widget-addon[data-id='" + lightBoxWidgetId + "']");
         if ($gallery.length == 0) {
            return;
         }
         //Get items array
         let $arGalleryItems = $gallery.find('.gallery-item a');
         //Check if we find items and requesting index is in range
         if (($arGalleryItems.length == 0) && ($arGalleryItems.length > lightBoxItemIndex)) {
            return;
         }
         //Get a requested item
         let $galleryItem = $($arGalleryItems[lightBoxItemIndex]);

         let un='bgae-lightbox-page-url';
         
         //Extract an item URL
         let url = $galleryItem.data(un);

         //TODO: add bgae-...
         //Detect data flag corresponding to the caption type
         let isLinkDataName = 'lightbox-title-is-link';
         if (captionType == 'description') {
            isLinkDataName = 'lightbox-description-is-link';
         }
         
         
         //Check if it's a link in a caption is enabled
         let isLinkEnabled = $galleryItem.data(isLinkDataName);

         if ('undefined' === typeof isLinkEnabled || isLinkEnabled !== 'yes') {
            return;
         }

         
         //TODO: check if an URL is correct
         //Check if the item has an URL data
         if (url.length == 0) {
            return;
         }
         //Navigate to the URL
         window.location.assign(url);

      }
   };

   //Init and start hangling
   BgaeLightbox.run();
});

