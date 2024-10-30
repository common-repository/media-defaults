(function ($, _) {
    'use strict';

    var media = wp.media, attachmentFilter;

    if (media) {
        // Get the localised sswMd object
        attachmentFilter = sswMd.inserting.attachment_filter;
        
        // Change the template used by the gallery settings panel
        media.view.Settings.Gallery.prototype.template = wp.template('gallery-settings-sswmd');
    
        /*
         * Replace the initialisation function for AttachmentDisplay
         * 
         * This is necessary because simply replacing the template (as done above for Gallery)
         * does not work. The default selections from the template are being changed by code
         * somewhere else - even when the HTML created sets the required "selected" attribute
         * correctly.
         */
        media.view.Settings.AttachmentDisplay.prototype.initialize = function() {
            var attachment = this.options.attachment;

            _.defaults(this.options, {
                userSettings: false
            });

            // The next block of conditions sets the default <option> elements
            if (typeof sswMd.inserting.attachment_display_alignment !== 'undefined') {
                this.model.set('align', sswMd.inserting.attachment_display_alignment);
            }
            if (typeof sswMd.inserting.attachment_display_size !== 'undefined') {
                this.model.set('size', sswMd.inserting.attachment_display_size);
            }
            
            if (this.model.get('canEmbed') && 
                typeof sswMd.inserting.attachment_display_link_to_embeddable !== 'undefined') {
                this.model.set('link', sswMd.inserting.attachment_display_link_to_embeddable);
            } else if (typeof sswMd.inserting.attachment_display_link_to !== 'undefined') {
                this.model.set('link', sswMd.inserting.attachment_display_link_to);
            }
            
            // Call 'initialize' directly on the parent class.
            media.view.Settings.prototype.initialize.apply(this, arguments);
            this.listenTo(this.model, 'change:link', this.updateLinkTo);

            if (attachment) {
                attachment.on('change:uploading', this.render, this);
            }
        };

        media.view.MediaFrame.Select.prototype.initialize = function() {
            
            media.view.MediaFrame.prototype.initialize.apply(this, arguments);
            var initialState = arguments[0].state;

            /*
             * Create the _library object based on the value of sswMd.inserting.attachment_filter
             */
            var _library;
            switch (attachmentFilter) {
            
                case 'uploaded' :
                    _library = {
                        uploadedTo: media.view.settings.post.id,
                        orderby: 'menuOrder',
                        order: 'ASC'
                    };
                    break;
            
                case 'image' :
                case 'audio' :
                case 'video' :
                    _library = {
                        type: attachmentFilter,
                        order: 'DESC',
                        orderby: 'date'
                    };
                    break;
            
                case 'unattached' :
                    _library = {
                        order: 'ASC',
                        orderby: 'menuOrder',
                        uploadedTo: 0
                    };                    
                    break;
                    
                default :
                    _library = {
                        order: 'DESC',
                        orderby: 'date'
                    };                    
                    break;
            }
            
            _.defaults(this.options, {
                selection: [],
                library: _library,
                multiple: false,
                state: 'library'
            });
            
            this.createSelection();
            this.createStates();
            this.bindHandlers();
            //console.log(this.states);

            this.states.forEach(function (state) {
                var library = state.get('library');
                /*
                 * This foreach loop controls what is displayed in the main modal panel when 
                 * changing acitivities like "Insert Media", "Create Gallery", 
                 * "Create Video Playlist", etc.
                 * 
                 * var type returns the correct library type for the activity
                 */
                var type = (function (stateId) {
                    switch (stateId) {
                        case 'gallery' :
                        case 'gallery-edit' :
                        case 'gallery-library' :
                        case 'edit-image' :
                        case 'featured-image' :
                            return 'image';
                            break;

                        case 'playlist' :
                        case 'playlist-edit' :
                        case 'playlist-library' :
                            return 'audio';
                            break;

                        case 'video-playlist' :
                        case 'video-playlist-edit' :
                        case 'video-playlist-library' :
                            return 'video';
                            break;

                        default :
                            return _library.type;
                            break;
                    }
                })(state.id);

                if (library) {
                    //console.log(library);
                    if (typeof _library.uploadedTo !== 'undefined') {
                        library.props.set('uploadedTo', _library.uploadedTo);
                    }
                    if (typeof type !== 'undefined' && initialState !== 'gallery-edit') {
                        library.props.set('type', type);
                    }
                    library.props.set('orderby', _library.orderby);
                    library.props.set('order', _library.order);
                }
            });

        };
        
        media.controller.FeaturedImage.prototype.initialize = function () {
            //console.log(this);

            var mediaQuery, library, comparator;
            
            mediaQuery = {
                type: 'image'
            };

            switch (attachmentFilter) {
            
                case 'uploaded' :
                    mediaQuery.uploadedTo = media.view.settings.post.id;
                    mediaQuery.order = 'ASC';
                    mediaQuery.orderby = 'menuOrder';
                    break;
            
                case 'unattached' :
                    mediaQuery.order = 'ASC';
                    mediaQuery.orderby = 'menuOrder';
                    mediaQuery.uploadedTo = 0;
                    break;
                    
                default :
                    mediaQuery.order = 'DESC';
                    mediaQuery.orderby = 'date';
                    break;
            }

            if (!this.get('library')) {
                this.set('library', media.query(mediaQuery));
            }

            media.controller.Library.prototype.initialize.apply(this, arguments);

            library = this.get('library');
            comparator = library.comparator;

            library.comparator = function (a, b) {
                var aInQuery = !!this.mirroring.get(a.cid),
                        bInQuery = !!this.mirroring.get(b.cid);

                if (!aInQuery && bInQuery) {
                    return -1;
                } else if (aInQuery && !bInQuery) {
                    return 1;
                } else {
                    return comparator.apply(this, arguments);
                }
            };

            library.observe(this.get('selection'));
        };
    }

})(jQuery, _);
