var content =
{
    cid: null,
    uid: null,
    account: null,

    $block: '.content-block',

    init: function()
    {
        content.account = SID;

        content.elements();
        content.comments.get();
        content.screenshots.init();
        content.screenshots.get();
    },

    // setup elements
    elements: function()
    {
        content.$block = $(content.$block);
        content.comments.$comments = $(content.comments.$comments);
        content.comments.$input = $(content.comments.$input);
        content.comments.$errors = $(content.comments.$errors);
        content.comments.$nocomments = $(content.comments.$nocomments);

        content.screenshots.$dropzone = $(content.screenshots.$dropzone);
        content.screenshots.$noscreenshots = $(content.screenshots.$noscreenshots);
        content.screenshots.$screenshots = $(content.screenshots.$screenshots);

        // set some ids
        content.cid = parseInt(content.$block.attr('data-contentid'));
        content.uid = parseInt(content.$block.attr('data-id'));

        // on click of tab nav
        $('.site').on('click', '.tab-nav span', function()
        {
            $('.tab-nav span.active').removeClass('active');
            $(this).addClass('active');

            var tab = $(this).attr('data-tab');
            if (!tab) {
                return;
            }

            $('.tab-block.active').removeClass('active');
            $('#tab-block-' + tab).addClass('active');

            if (typeof XIVDBTooltips !== 'undefined') {
                XIVDBTooltips.get();
            }
        });

        // submit comment
        $('.site').on('click', '.comment-submit', function()
        {
            var message = content.comments.$input.val().trim();
            content.comments.post(message);
        });

        // reply click
        $('.site').on('click', '.reply-button', function()
        {
            var id = $(this).attr('data-id');
            content.comments.reply(id);
        });

        // reply submit
        $('.site').on('click', '.reply-submit', function()
        {
            var $reply = $('.ui-comment-reply').find('.comment-reply'),
                message = $reply.val().trim(),
                reply = $reply.attr('data-reply');

            content.comments.post(message, reply)
        });

        // minimize
        $('.site').on('click', '.toggle-comment', function()
        {
            var id = $(this).attr('data-id'),
                visible = parseInt($(this).attr('data-visible')),
                $parent = $('#ui-comment-' + id);

            if (visible)
            {
                $(this).attr('data-visible', 0);
                $(this).removeClass('fa-minus-square').addClass('fa-plus-square');
                $parent.find('.comment_detail').eq(0).hide();
                $parent.find('.vote_box').eq(0).hide();
                $parent.find('.comment_minimized').eq(0).show();
            }
            else
            {
                $(this).attr('data-visible', 1);
                $(this).removeClass('fa-plus-square').addClass('fa-minus-square');
                $parent.find('.comment_detail').eq(0).show();
                $parent.find('.vote_box').eq(0).show();
                $parent.find('.comment_minimized').eq(0).hide();
            }
        });

        // edit post
        $('.site').on('click', '.edit-button', function()
        {
            var id = $(this).attr('data-id');

            $comment = $('#ui-comment-' + id);
            $comment.find('.text').eq(0).hide();
            $comment.find('.comment-edit').eq(0).show();
            $comment.find('.stop-edit-button').eq(0).show();
            $(this).hide();


            $textarea = $comment.find('.comment-edit').eq(0).find('textarea');
            autosize($textarea);
        });

        // stop edit post
        $('.site').on('click', '.stop-edit-button', function()
        {
            var id = $(this).attr('data-id'),
                $comment = $('#ui-comment-' + id);

            $comment.find('.text').eq(0).show();
            $comment.find('.comment-edit').eq(0).hide();
            $comment.find('.edit-button').eq(0).show();
            $(this).hide();
        });

        // save button
        $('.site').on('click', '.save-button', function()
        {
            var id = $(this).attr('data-id');
                $comment = $('#ui-comment-' + id);

            $(this).disable('Please wait ...');

            var message = $comment.find('.comment-edit textarea').val().trim();
            content.comments.save(message, id);
        });

        // delete button
        $('.site').on('click', '.delete-button', function(e)
        {
            if (!window.confirm('Are you sure you want to delete this comment?')) {
                e.preventDefault();
                return;
            }

            var id = $(this).attr('data-id');
            $(this).disable('Please wait ...');
            content.comments.delete(id);
        });

        // delete screenshot
        $('.site').on('click', '.screenshot-delete', function(e)
        {
            if (!window.confirm('Are you sure you want to delete this screenshot?')) {
                e.preventDefault();
                return;
            }

            var id = $(this).attr('data-id');
            $(this).disable('Please wait ...');
            content.screenshots.delete(id);
        });
    },

    // comments stuff
    comments:
    {
        maxIndentation: 4,
        $comments: '.ui-comments',
        $input: '.comment-input',
        $errors: '.comment-errors',
        $nocomments: '.no-comments',

        error: function(text)
        {
            content.comments.$errors.html('<div class="alert alert-error"><h5>Oops!</h5>'+ text +'</div>').show();
        },

        // get comments
        get: function()
        {
            content.comments.$errors.hide();

            // ajax results
            $.ajax(
            {
                url: content.urls.get('comments'),
                cache: false,
                success: function(data)
                {
                    if (typeof data === 'object') {
                        content.comments.render(data);
                        return;
                    }

                    console.error('Comments data invalid');
                },
                error: function(data, status, error)
                {
                    console.error(data, status, error);
                },
                complete: function()
                {
                    ttdrop();
                }
            });
        },

        // post a comment
        post: function(message, reply)
        {
            content.comments.$errors.hide();

            // check length
            if (message && message.length > 2 && message.length < 2000)
            {
                $.ajax(
                {
                    url: content.urls.get('commentsPost'),
                    method: 'POST',
                    data:
                    {
                        uid: content.uid,
                        cid: content.cid,
                        message: message,
                        reply: reply,
                    },
                    success: function(data)
                    {
                        if (data)
                        {
                            content.comments.$input.val('');
                            content.comments.get();
                            return;
                        }

                        content.comments.$errors.append(data[1]);
                    },
                    error: function(data, status, error)
                    {
                        content.comments.error('There was an error! Please try again');
                        console.log(data.responseText);
                        console.error(data.responseText, status, error);
                    },
                    complete: function()
                    {

                    }
                });
            }
        },

        save: function(message, id)
        {
            content.comments.$errors.hide();

            if (id && message && message.length > 2 && message.length < 2000)
            {
                $.ajax(
                {
                    url: content.urls.get('commentsUpdate'),
                    method: 'POST',
                    data:
                    {
                        uid: content.uid,
                        cid: content.cid,
                        message: message,
                        postId: id,
                    },
                    success: function(data)
                    {
                        if (data[0])
                        {
                            content.comments.$input.val('');
                            content.comments.get();
                            return;
                        }

                        content.comments.$errors.append(data[1]);
                    },
                    error: function(data, status, error)
                    {
                        content.comments.error('There was an error! Please try again');
                        console.log(data.responseText);
                        console.error(data.responseText, status, error);
                    },
                    complete: function()
                    {

                    }
                });
            }
        },

        delete: function(id)
        {
            content.comments.$errors.hide();

            if (id)
            {
                $.ajax(
                {
                    url: content.urls.get('commentsDelete'),
                    method: 'POST',
                    data:
                    {
                        uid: content.uid,
                        cid: content.cid,
                        postId: id,
                    },
                    success: function(data)
                    {
                        if (data[0])
                        {
                            content.comments.get();
                            return;
                        }

                        content.comments.$errors.append(data[1]);
                    },
                    error: function(data, status, error)
                    {
                        content.comments.error('There was an error! Please try again');
                        console.log(data.responseText);
                        console.error(data.responseText, status, error);
                    },
                    complete: function()
                    {

                    }
                });
            }
        },

        // reply to a comment
        reply: function(id)
        {
            // remove existing reply boxes
            $('.ui-comment-reply').remove();

            // get element for comment
            var $element = $('#ui-comment-' + id);

            // find reply box
            $replyto = $element.find('.replyto').eq(0);

            // show reply to and inject reply comment form
            $replyto.show().html(render('#ui-comment-reply', { id: id, }));

            // trigger auto resize on the new textarea
            autosize($('.ui-comment-reply').find('textarea'));
        },

        // render comments
        render: function(data)
        {
            // empty comments
            content.comments.$nocomments.hide();
            content.comments.$comments.empty().html('');

            // if length
            if (data.length > 0)
            {
                content.comments.renderRecurrsion(data, content.comments.$comments, 0);
            }
            else
            {
                content.comments.$nocomments.show();
            }

            // remove empty replies
            content.comments.$comments.find('.replies').each(function()
            {
                if ($(this).html().length == 0)
                {
                    $(this).remove();
                }
            });

            if (typeof XIVDBTooltips !== 'undefined') {
                XIVDBTooltips.get();
            }
        },

        renderRecurrsion: function(data, $element, level)
        {
            level++;

            if (level > content.comments.maxIndentation) {
                return;
            }

            for (var i in data)
            {
                var comment = data[i];

                // fix time to a moment time
                comment.time = moment(comment.time).fromNow();

                // fix edited to a edited time
                if (comment.edited == '0000-00-00 00:00:00') {
                    comment.edited = null;
                }

                if (comment.edited) {
                    comment.edited = moment(comment.edited).fromNow();
                }

                // of no character
                if (!comment.character_avatar) {
                    comment.character_avatar = '/img/ui/noavatar_light.png';
                }

                // vote bools for mustache
                comment.voted_yes = (comment.vote_score == 1) ? true : false;
                comment.voted_no = (comment.vote_score == -1) ? true : false;

                // is author
                comment.is_author = false;
                if (content.account == comment.member) {
                    comment.is_author = true;
                }

                // level of indentation
                comment.level = level;

                // if can reply or not
                comment.can_reply = true;
                if (level > (content.comments.maxIndentation-1)) {
                    comment.can_reply = false;
                }

                // if has replies or not
                comment.has_replies = false;
                var replySize = Object.size(comment.replies);
                if (replySize > 0) {
                    comment.has_replies = true;
                }

                // points class
                comment.points_class = 'light';
                if (comment.points > 0) {
                    comment.points_class = 'green';
                } else if (comment.points < 0) {
                    comment.points_class = 'red';
                }

                // is online or not
                comment.is_online = false;
                if (content.account) {
                    comment.is_online = true;
                }

                //console.log(comment);

                // append comment
                $element.append(render('#ui-comment', comment));

                // if replies
                if (replySize > 0)
                {
                    var $reply = $('#ui-comment-' + comment.id +' .replies');
                    content.comments.renderRecurrsion(comment.replies, $reply, level);
                }
            }
        }
    },

    // screenshot stuff
    screenshots:
    {
        $dropzone: '.screenshot-form',
        $noscreenshots: '.no-screenshots',
        $screenshots: '.screenshots',

        maxSize: 10000,
        uploading: false,
        responded: false,

        error: function(text)
        {
            content.screenshots.$dropzone.html('<h5>Oops!</h5><p>'+ text +'</p>').addClass('fail').css({'background-image' : 'none'});
        },

        setDefaultDisplay: function()
        {
            content.screenshots.$dropzone.removeClass('active uploading fail');
            content.screenshots.$dropzone.css({'background-image' : 'none'});
            content.screenshots.$dropzone.html('<span class="blue">Upload Screenshot</span>');
            content.screenshots.$dropzone.append('<div class="fs16 mt10">Drag an image onto this block to upload</div>');
        },

        addProgressBar: function()
        {
            content.screenshots.$dropzone.append('<div class="progress"><span class="inner thick blue" style="width:0%"></span></div>');
        },

        get: function()
        {
            // ajax results
            $.ajax(
            {
                url: content.urls.get('screenshots'),
                cache: false,
                success: function(data)
                {
                    if (typeof data === 'object') {
                        content.screenshots.render(data);
                        return;
                    }

                    console.error('Screenshots data invalid');
                },
                error: function(data, status, error)
                {
                    console.error(data, status, error);
                },
                complete: function()
                {
                    ttdrop();
                    AppContentScreenshots.checkForLightbox();
                }
            });
        },

        render: function(data)
        {
            content.screenshots.$screenshots.empty().html('');

            if (data.length > 0)
            {
                content.screenshots.$noscreenshots.hide();
                var $element = content.screenshots.$screenshots;

                for (var i in data)
                {
                    var image = data[i];

                    // fix time to a moment time
                    image.time = moment(image.time).fromNow();

                    // is author
                    image.is_author = false;
                    if (content.account == image.member) {
                        image.is_author = true;
                    }

                    $element.append(render('#ui-image', image));
                }
            }
            else
            {
                content.screenshots.$noscreenshots.show();
            }

            if (typeof XIVDBTooltips !== 'undefined') {
                XIVDBTooltips.get();
            }
        },

        delete: function(id)
        {
            if (id)
            {
                $.ajax(
                {
                    url: content.urls.get('screenshotsDelete'),
                    method: 'POST',
                    data:
                    {
                        uid: content.uid,
                        cid: content.cid,
                        screenshotId: id,
                    },
                    success: function(data)
                    {
                        if (data[0])
                        {
                            content.screenshots.get();
                            return;
                        }

                        console.log(data);
                    },
                    error: function(data, status, error)
                    {
                        content.comments.error('There was an error! Please try again');
                        console.log(data.responseText);
                        console.error(data.responseText, status, error);
                    },
                    complete: function()
                    {

                    }
                });
            }
        },

        init: function()
        {
            var dropzone = document.getElementById('screenshot-form');

            content.screenshots.$dropzone.on('dragenter', content.screenshots.dragenter);
            content.screenshots.$dropzone.on('dragover', content.screenshots.dragover);
            content.screenshots.$dropzone.on('dragleave', content.screenshots.dragleave);
            content.screenshots.$dropzone.on('drop dragdrop', content.screenshots.drop);
        },

        dragenter: function(e)
        {
            // prevent things
            e.stopPropagation();
            e.preventDefault();
        },

        dragover: function(e)
        {
            // prevent things
            e.stopPropagation();
            e.preventDefault();

            content.screenshots.$dropzone.addClass('active').removeClass('fail');
        },

        dragleave: function(e)
        {
            // remove active
            content.screenshots.$dropzone.removeClass('active uploading fail');
        },

        drop: function(e)
        {
            if (content.screenshots.uploading) {
                return;
            }

            // prevent things
            e.stopPropagation();
            e.preventDefault();

            content.screenshots.$dropzone.removeClass('active fail');
            content.screenshots.$dropzone.addClass('uploading');

            var dt = e.dataTransfer || (e.originalEvent && e.originalEvent.dataTransfer);
            var files = e.target.files || (dt && dt.files);
            var file = null;

            if (files)
            {
                file = files[0];
                content.screenshots.upload(file);
            }
            else
            {
                content.screenshots.error('Could not detect files, try again or contact support');
            }
        },

        upload: function(file)
        {
            content.screenshots.responded = false;
            content.screenshots.uploading = true;
            content.screenshots.$dropzone.html('<span class="upload-status">Uploading ...</span>');

            // some data stuff
            var name = file.name,
                size = file.size,
                type = file.type,
                sizeInKb = size / 1024,
                sizeInMb = (sizeInKb / 1024).toFixed(2),
                maxSizeInMb = (content.screenshots.maxSize / 1024).toFixed(2);

            // check size
            if (sizeInMb > maxSizeInMb)
            {
                content.screenshots.error('File size is too big <span class="red">('+ sizeInMb +')</span>, keep below: <span class="yellow">' + maxSizeInMb + '</span> MB!');
            }
            // check type
            else if (!type.match('image.*'))
            {
                content.screenshots.error('The file dropped was not an image, drop only: <span class="yellow">PNG, JPG or GIF</span>');
            }
            // ok
            else
            {
                // Open a reader
                var reader = new FileReader();
                reader.onload = (function(temp)
                {
                    return function(e)
                    {
                        var img = new Image;
                        img.onload = function() {

                            // Set Image Data
                            var data = e.target.result;

                            // Set preview
                            $('.screenshot-form').css({ 'background-image':'url(' + data + ')' });

                            // Setup new form
                            var upload = new FormData();
                            upload.append("uid", content.uid);
                            upload.append("cid", content.cid);
                            upload.append("name", name);
                            upload.append("type", type);
                            upload.append("size", size);
                            upload.append("data", data);

                            // Get an XMLHttpRequest instance
                            var xhr = new XMLHttpRequest();

                            // Attach event listeners to the xhr
                            xhr.upload.addEventListener('loadstart', content.screenshots.uploadStart, false);
                            xhr.upload.addEventListener('progress', content.screenshots.uploadProgress, false);
                            xhr.upload.addEventListener('load', content.screenshots.uploadComplete, false);
                            xhr.addEventListener('readystatechange', content.screenshots.uploadResponse, false);

                            // Open post request
                            xhr.open("POST", content.urls.get('screenshotsUpload'), true);
                            xhr.send(upload);
                        };

                        // Checking image resolution
                        img.src = e.target.result;
                    };
                })(file);

                // Read in the image file as a data URL.
                reader.readAsDataURL(file);
            }
        },

        // an upload starts
        uploadStart: function(e)
        {
            // add progress bar
            content.screenshots.addProgressBar();
        },

        // upload progress response
        uploadProgress: function(e)
        {
            var percent = e.loaded / e.total * 100;
            content.screenshots.$dropzone.find('.progress span').width(percent + '%');
        },

        // upload complete
        uploadComplete: function(e)
        {
            content.screenshots.$dropzone.find('.upload-status').html('Uploaded! - Saving ...');
            content.screenshots.$dropzone.find('.progress span').removeClass('blue').addClass('green');
            content.screenshots.uploading = false;

            setTimeout(function() {
                content.screenshots.$dropzone.find('.upload-status').html('Compressing image ...');
            }, 3000);

            setTimeout(function() {
                content.screenshots.$dropzone.find('.upload-status').html('Making smaller versions ...');
            }, 6000);

            setTimeout(function() {
                content.screenshots.$dropzone.find('.upload-status').html('Cleaning up! Almost done!');
            }, 9000);
        },

        // upload response
        uploadResponse: function(e)
        {
            if (content.screenshots.responded) {
                return;
            }

            // get status
            var status = null;
            try { status = e.target.status; } catch(e) { return; }

            // check status
            if (status == 200 && e.target.responseText)
            {
                content.screenshots.responded = true;
                var response = e.target.responseText;

                // try parse json response
                try
                {
                    var response = JSON.parse(response);

                    // respond
                    if (response[0])
                    {
                        content.screenshots.setDefaultDisplay();
                        content.screenshots.get();
                    }
                    else
                    {
                        content.screenshots.error(response[1]);
                    }
                }
                catch(e)
                {
                    console.error(response);
                    content.screenshots.error('There was an unknown error uploading, please contact support.');
                }
            }
        },
    },

    // urls
    urls:
    {
        list:
        {
            comments: '/comments/get/{cid}/{uid}',
            commentsUpdate: '/comments/update',
            commentsDelete: '/comments/delete',
            commentsPost: '/comments/post',

            screenshots: '/screenshots/get/{cid}/{uid}',
            screenshotsUpload: '/screenshots/upload',
            screenshotsDelete: '/screenshots/delete',
        },

        get: function(type, message)
        {
            var url = content.urls.list[type];

            url = url.replace('{cid}', content.cid);
            url = url.replace('{uid}', content.uid);

            return url;
        }
    },
}
