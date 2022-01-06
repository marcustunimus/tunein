var inputFilesElement;
var uploadedFiles = [];
var removedAttachment = false;
var postFilesLastIndex = -1;
var removedPostFiles = [];
var autoHideFlashMessage;
var commentsArray = [];

function addFilesToForm(elementId) {
    let postForm = document.getElementById(elementId);

    postForm.onsubmit = function (e) {
        dt = new DataTransfer();

        let filesContainer = document.getElementById('files-input-container');
        let removedPostFilesElement = document.createElement("input"); removedPostFilesElement.type = "hidden"; removedPostFilesElement.name = "removedPostFiles";
        removedPostFilesElement.value = removedPostFiles.join("/");

        filesContainer.appendChild(removedPostFilesElement);

        for (let uploadedFile of uploadedFiles) {
            dt.items.add(uploadedFile);
        }

        inputFilesElement.files = dt.files;
    }
}

function showUploadedFilesPreview(name, path, previewContainer) {
    let uploadsContainer = document.getElementById('uploads');
    let postFilesContainer = document.getElementById('post-files');

    inputFilesElement = document.getElementById(name);

    inputFilesElement.onclick = function () {
        inputFilesElement.value = null;
    }

    inputFilesElement.onchange = function () {
        uploadsContainer.innerHTML = "";
        previewContainer.innerHTML = "";

        if (typeof postFilesContainer !== 'undefined') {
            uploadsContainer.appendChild(postFilesContainer);
        }

        lastPostPreviewIndex = postFilesLastIndex - removedPostFiles.length;

        for (let i = (lastPostPreviewIndex === -1 ? 0 : lastPostPreviewIndex + 1); i < postFilesContainer.childNodes.length;) {
            postFilesContainer.childNodes[i].remove();
        }

        getUploadedFiles(inputFilesElement.files);

        showUploadedFiles(uploadedFiles, path);
    }
}

function getUploadedFiles(files) {
    for (let file of files) {
        let duplicate = false;

        duplicate = false;

        for (let uploadedFile of uploadedFiles) {
            if (uploadedFile.name === file.name && uploadedFile.type === file.type && uploadedFile.size === file.size) {
                duplicate = true;

                break;
            }
        }

        if (!duplicate) {
            uploadedFiles = uploadedFiles.concat(file);
        }
    }
}

function showUploadedFiles(files, path) {
    let postFilesContainer = document.getElementById('post-files');

    for (let file of files) {
        let reader = new FileReader();
        let filePreview = document.createElement("div"); filePreview.setAttribute("title", file.name); filePreview.setAttribute("style", "z-index: 1;");
        let fileShowcase = document.createElement("figure"); fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
        let fileCaption = document.createElement("figcaption"); fileCaption.innerText = file.name; fileCaption.setAttribute("class", "post-file-upload-caption");
        let imageContainer = document.createElement("img");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-thumbnail-preview-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";

        closeButtonContainer.onclick = function () {
            removedAttachment = true;

            for (let i = 0; i < files.length; i++) {
                if (files[i].name === file.name && files[i].type === file.type && files[i].size === file.size) {
                    files.splice(i, 1);

                    break;
                }
            }

            postFilesContainer.removeChild(filePreview);
        }

        if (file.type.match('video.mp4') || file.type.match('video.webm')) {
            let videoContainer = document.createElement("div"); videoContainer.setAttribute("class", "post-file-upload-video-thumbnail");
            let playButton = document.createElement("div"); playButton.setAttribute("class", "play-movie-button"); playButton.style = "background-image: url(" + path + "/images/movie_white_24dp.svg);";

            videoContainer.appendChild(playButton);
            fileShowcase.appendChild(videoContainer); fileShowcase.appendChild(fileCaption);
        }
        else if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
            imageContainer.id = "previewImage"; imageContainer.setAttribute("src", ""); imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");

            fileShowcase.appendChild(imageContainer); fileShowcase.appendChild(fileCaption);
        }
        else {
            let fileContainer = document.createElement("div"); fileContainer.setAttribute("class", "post-file-upload-unknown-file-thumbnail");
            let unknownFile = document.createElement("div"); unknownFile.setAttribute("class", "unknown-file"); unknownFile.style = "background-image: url(" + path + "/images/report_problem_white_24dp.svg);";

            fileContainer.appendChild(unknownFile);
            fileShowcase.appendChild(fileContainer); fileShowcase.appendChild(fileCaption);
        }

        closeButtonContainer.appendChild(closeButton);
        fileShowcase.appendChild(fileCaption);
        filePreview.appendChild(fileShowcase); filePreview.appendChild(closeButtonContainer);
        postFilesContainer.appendChild(filePreview);

        reader.onload = function () {
            if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
                imageContainer.setAttribute("src", reader.result);
            }

            loadPreviewFromReaderButton(file, reader, filePreview, path, previewContainer);
        }

        reader.readAsDataURL(file);
    }
}

function loadPreviewFromReaderButton(file, reader, filePreview, path, previewContainer) {
    filePreview.setAttribute("class", "cursor-pointer");

    filePreview.onclick = function () {
        if (removedAttachment) {
            removedAttachment = false;
            return;
        }

        if (file.size <= 40 * 1024 * 1024) {
            previewContainer.innerHTML = "";

            previewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (file.type.match('video.mp4') || file.type.match('video.webm')) {
                let videoContainer = document.createElement("video"); videoContainer.setAttribute("controls", "");
                videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center");
                let videoSource = document.createElement("source"); videoSource.setAttribute("src", reader.result); videoSource.setAttribute("type", file.type);

                videoContainer.appendChild(videoSource);
                previewContainer.appendChild(videoContainer);
            }
            else if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
                let imageContainer = document.createElement("img"); imageContainer.setAttribute("src", reader.result); imageContainer.setAttribute("class", "preview center");

                previewContainer.appendChild(imageContainer);
            }
            else {
                let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center");
                unknownFileContainer.innerText = "Unknown File Type";
                unknownFileContainer.onclick = function () { hidePreview(previewContainer); }

                previewContainer.appendChild(unknownFileContainer);
            }

            previewContainer.appendChild(closeButtonContainer);

            previewBackground.onclick = function () { hidePreview(previewContainer); }

            closeButtonContainer.onclick = function () { hidePreview(previewContainer); }
        }
    }
}

function hidePreview(previewContainer) {
    previewContainer.style = "";
    previewContainer.innerHTML = "";

    if (document.getElementById('preview').innerHTML !== "") {
        document.body.style.overflow = 'hidden';
    }
    else {
        document.body.style = "";
    }
}

function showPostFilesPreview(filesInStringFormat, path) {
    if (filesInStringFormat === "") {
        return;
    }

    postFilesInStringFormat = filesInStringFormat;

    let filesContainer = document.getElementById('files-input-container');
    let postFilesContainer = document.getElementById('post-files');

    var files = filesInStringFormat.split("|");

    postFilesLastIndex = files.length / 3 - 1;

    removedPostFiles = [];

    if (files.length !== 0) {
        for (let i = 0; i < files.length; i += 3) {
            let filePreview = document.createElement("div"); filePreview.setAttribute("title", files[i]); filePreview.setAttribute("style", "z-index: 1;");
            let fileShowcase = document.createElement("figure"); fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
            let fileCaption = document.createElement("figcaption"); fileCaption.innerText = files[i]; fileCaption.setAttribute("class", "post-file-upload-caption");
            let imageContainer = document.createElement("img");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-thumbnail-preview-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";

            closeButtonContainer.onclick = function () {
                removedPostFiles.push(files[i]);
                removedAttachment = true;

                postFilesContainer.removeChild(filePreview);
            }

            if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                let videoContainer = document.createElement("div"); videoContainer.setAttribute("class", "post-file-upload-video-thumbnail");
                let playButton = document.createElement("div"); playButton.setAttribute("class", "play-movie-button"); playButton.style = "background-image: url(" + path + "/images/movie_white_24dp.svg);";

                videoContainer.appendChild(playButton);
                fileShowcase.appendChild(videoContainer); fileShowcase.appendChild(fileCaption);
            }
            else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]); imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");

                fileShowcase.appendChild(imageContainer); fileShowcase.appendChild(fileCaption);
            }
            else {
                let fileContainer = document.createElement("div"); fileContainer.setAttribute("class", "post-file-upload-unknown-file-thumbnail");
                let unknownFile = document.createElement("div"); unknownFile.setAttribute("class", "unknown-file"); unknownFile.style="background-image: url(" + path + "/images/report_problem_white_24dp.svg);";

                fileContainer.appendChild(unknownFile);
                fileShowcase.appendChild(fileContainer); fileShowcase.appendChild(fileCaption);
            }

            closeButtonContainer.appendChild(closeButton);
            fileShowcase.appendChild(fileCaption);
            filePreview.appendChild(fileShowcase); filePreview.appendChild(closeButtonContainer);
            postFilesContainer.appendChild(filePreview);

            loadPreviewUploadedPostFileButton(files, i, filePreview, path, previewContainer);
        }
    }
}

function loadPreviewUploadedPostFileButton(files, i, filePreview, path, previewContainer) {
    filePreview.setAttribute("class", "cursor-pointer");

    filePreview.onclick = function () {
        if (removedAttachment) {
            removedAttachment = false;
            return;
        }

        if (parseInt(files[i + 2]) <= 40 * 1024 * 1024) {
            previewContainer.innerHTML = "";

            previewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                let videoContainer = document.createElement("video"); videoContainer.setAttribute("controls", "");
                videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center");
                let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                videoContainer.appendChild(videoSource);
                previewContainer.appendChild(videoContainer);
            }
            else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                let imageContainer = document.createElement("img"); imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]); imageContainer.setAttribute("class", "preview center");

                previewContainer.appendChild(imageContainer);
            }
            else {
                let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center");
                unknownFileContainer.innerText = "Unknown File Type"
                unknownFileContainer.onclick = function () { hidePreview(previewContainer); }

                previewContainer.appendChild(unknownFileContainer);
            }

            previewContainer.appendChild(closeButtonContainer);

            previewBackground.onclick = function () { hidePreview(previewContainer); }
            closeButtonContainer.onclick = function () { hidePreview(previewContainer); }
        }
    }
}

function loadPostFiles(postId, filesInStringFormat, path, previewContainer) {
    if (filesInStringFormat === "") {
        return;
    }

    let postContentContainer = document.getElementById("postContent" + postId);
    let postFilesContentContainer = document.createElement("div"); postFilesContentContainer.setAttribute("class", "post-files-container");
    let followingElementContainer = document.getElementById("post-" + postId + "-info-container");
    let files = filesInStringFormat.split("|");

    if (files.length !== 0) {
        switch (files.length / 3) {
            case 1: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-one");


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-two-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);
                        let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
                        let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style = "background-image: url(" + path + "/images/play_circle_white_24dp.svg);";

                        playButtonContainer.appendChild(playButton);
                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                        postFilePreview.appendChild(playButtonContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]);
                        imageContainer.setAttribute("class", "post-file-image-two-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
                }

                break;
            };
            case 2: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-one-per-column");


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-two-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);
                        let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
                        let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style = "background-image: url(" + path + "/images/play_circle_white_24dp.svg);";

                        playButtonContainer.appendChild(playButton);
                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                        postFilePreview.appendChild(playButtonContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]);
                        imageContainer.setAttribute("class", "post-file-image-two-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
                }

                break;
            };
            case 3: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview;

                    if (i === 0) {
                        postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-one-per-column");
                    }
                    else {
                        postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-two-per-row");
                    }


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video");

                        if (i === 0) {
                            videoContainer.setAttribute("class", "post-file-video-two-column");
                        }
                        else {
                            videoContainer.setAttribute("class", "post-file-video-one-column");
                        }

                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);
                        let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
                        let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style = "background-image: url(" + path + "/images/play_circle_white_24dp.svg);";

                        playButtonContainer.appendChild(playButton);
                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                        postFilePreview.appendChild(playButtonContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]);

                        if (i === 0) {
                            imageContainer.setAttribute("class", "post-file-image-two-column");
                        }
                        else {
                            imageContainer.setAttribute("class", "post-file-image-one-column");
                        }

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
                }

                break;
            };
            case 4: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview;

                    postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-two-per-row");


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-one-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);
                        let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
                        let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style = "background-image: url(" + path + "/images/play_circle_white_24dp.svg);";

                        playButtonContainer.appendChild(playButton);
                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                        postFilePreview.appendChild(playButtonContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]);

                        imageContainer.setAttribute("class", "post-file-image-one-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
                }

                break;
            };
            case 5: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview;

                    if (i === 0 || i === 1 * 3) {
                        postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-two-per-row");
                    }
                    else {
                        postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-three-per-row");
                    }


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-one-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);
                        let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
                        let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style = "background-image: url(" + path + "/images/play_circle_white_24dp.svg);";

                        playButtonContainer.appendChild(playButton);
                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                        postFilePreview.appendChild(playButtonContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]);

                        imageContainer.setAttribute("class", "post-file-image-one-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
                }

                break;
            };
            default: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview;

                    if (i === 0 || i === 1 * 3) {
                        postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-two-per-row");
                    }
                    else {
                        postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-three-per-row");
                    }


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-one-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);

                        if (i === 4 * 3) {
                            videoContainer.style = "filter: blur(0.25rem);"

                            let numberOfFilesMoreContainer = document.createElement("div"); numberOfFilesMoreContainer.setAttribute("class", "post-preview-centered-text-container");
                            let numberOfFilesMoreContent = document.createElement("span"); numberOfFilesMoreContent.innerText = "+" + (files.length / 3 - 4);

                            numberOfFilesMoreContainer.appendChild(numberOfFilesMoreContent);
                            postFilePreview.appendChild(numberOfFilesMoreContainer);
                        }
                        else {
                            let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
                            let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style = "background-image: url(" + path + "/images/play_circle_white_24dp.svg);";

                            playButtonContainer.appendChild(playButton);
                            postFilePreview.appendChild(playButtonContainer);
                        }
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]);
                        imageContainer.setAttribute("class", "post-file-image-one-column");

                        postFilePreview.appendChild(imageContainer);

                        if (i === 4 * 3) {
                            imageContainer.style = "filter: blur(0.25rem);"

                            let numberOfFilesMoreContainer = document.createElement("div"); numberOfFilesMoreContainer.setAttribute("class", "post-preview-centered-text-container");
                            let numberOfFilesMoreContent = document.createElement("span"); numberOfFilesMoreContent.innerText = "+" + (files.length / 3 - 4);

                            numberOfFilesMoreContainer.appendChild(numberOfFilesMoreContent);
                            postFilePreview.appendChild(numberOfFilesMoreContainer);
                        }
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);

                    if (i === 4 * 3) {
                        break;
                    }
                }

                break;
            };
        }
    }
}

function loadPreviewPostFileButton(files, i, filePreview, path, previewContainer) {
    filePreview.setAttribute("class", filePreview.getAttribute('class') + " cursor-pointer");

    filePreview.onclick = function () {
        loadPreviewPostFile(files, i, path, previewContainer);
    }
}

function loadPreviewPostFile(files, i, path, previewContainer) {
    previewContainer.innerHTML = "";

    previewContainer.style.zIndex = 100;
    document.body.style.overflow = 'hidden';

    let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
    let previewContent = document.createElement("div"); previewContent.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;"; previewContent.setAttribute("class", "preview center");
    let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
    let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";
    let nextButtonContainer = document.createElement("div"); nextButtonContainer.setAttribute("class", i + 3 < files.length ? "next-button-container" : "hidden");
    let nextButton = document.createElement("div"); nextButton.setAttribute("class", "next-button"); nextButton.style = "background-image: url(" + path + "/images/navigate_next_white_24dp.svg);";
    let beforeButtonContainer = document.createElement("div"); beforeButtonContainer.setAttribute("class", i - 3 >= 0 ? "before-button-container" : "hidden");
    let beforeButton = document.createElement("div"); beforeButton.setAttribute("class", "before-button"); beforeButton.style = "background-image: url(" + path + "/images/navigate_before_white_24dp.svg);";
    let skipFunction = false;

    closeButtonContainer.appendChild(closeButton);
    nextButtonContainer.appendChild(nextButton);
    beforeButtonContainer.appendChild(beforeButton);

    previewBackground.onclick = function () { hidePreview(previewContainer); }
    previewContent.onclick = function () {
        if (!skipFunction) {
            hidePreview(previewContainer);
        }
        else {
            skipFunction = false;
        }
    }

    previewContainer.appendChild(previewBackground);

    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
        let videoContainer = document.createElement("video"); videoContainer.setAttribute("controls", "");
        videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center noselect");
        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + "storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

        videoContainer.appendChild(videoSource);

        videoContainer.onclick = function () {
            skipFunction = true;
        }

        previewContent.appendChild(videoContainer);
        previewContainer.appendChild(previewContent);
    }
    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
        let imageContainer = document.createElement("img"); imageContainer.setAttribute("src", path + "storage/post_files/" + files[i]); imageContainer.setAttribute("class", "preview center noselect");

        imageContainer.onclick = function () {
            skipFunction = true;
        }

        previewContent.appendChild(imageContainer);
        previewContainer.appendChild(previewContent);
    }
    else {
        let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center noselect");
        unknownFileContainer.innerText = "Unknown File Type"
        unknownFileContainer.onclick = function () { hidePreview(previewContainer); }

        previewContainer.appendChild(unknownFileContainer);
    }

    closeButtonContainer.onclick = function () { hidePreview(previewContainer); }
    nextButtonContainer.onclick = function () {
        if (i + 3 < files.length) {
            loadPreviewPostFile(files, i + 3, path, previewContainer);
        }
    }
    beforeButtonContainer.onclick = function () {
        if (i - 3 >= 0) {
            loadPreviewPostFile(files, i - 3, path, previewContainer);
        }
    }

    previewContainer.appendChild(closeButtonContainer);
    previewContainer.appendChild(nextButtonContainer);
    previewContainer.appendChild(beforeButtonContainer);
}

function setInteractionButtonsFunctionality(postId, numberOfLikes, path, previewContainer) {
    let postLikeButtonContainer = document.getElementById("post-" + postId + "-like");
    let postCommentButtonContainer = document.getElementById("post-" + postId + "-comment");
    let postBookmarkButtonContainer = document.getElementById("post-" + postId + "-bookmark");
    let postLinkButtonContainer = document.getElementById("post-" + postId + "-link");
    let postInfoContainer = document.getElementById("post-" + postId + "-info");

    postLikeButtonContainer.onclick = function () {
        if (postLikeButtonContainer.firstElementChild.style == "background-image: url(" + path + "/images/favorite_border_white_24dp.svg);") {
            postLikeButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/favorite_white_24dp.svg);";
        }
        else if (postLikeButtonContainer.firstElementChild.style == "background-image: url(" + path + "/images/favorite_white_24dp.svg);") {
            postLikeButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/favorite_border_white_24dp.svg);";
        }

        try {
            fetch(path + 'post/' + postId + '/like', {
                method: 'POST',
                headers: {
                    'url': path + 'post/' + postId + '/like',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data === "Liked") {
                    postLikeButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/favorite_white_24dp.svg);";
                    numberOfLikes += 1;
                    postInfoContainer.innerText = numberOfLikes + " " + (numberOfLikes === 1 ? "like" : "likes");
                }
                else if (data === "Unliked") {
                    postLikeButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/favorite_border_white_24dp.svg);";
                    numberOfLikes -= 1;
                    postInfoContainer.innerText = numberOfLikes + " " + (numberOfLikes === 1 ? "like" : "likes");
                }
                else if (data === "Login") {
                    window.location.href = path + 'login';
                }
            }).catch(function (error) {
                window.location.href = path + 'login';
            });
        } catch (error) {
            window.location.href = path + 'login';
        }
    }

    postCommentButtonContainer.onclick = function () { viewCommentsWindow(path, postId, false); }

    postBookmarkButtonContainer.onclick = function () {
        if (postBookmarkButtonContainer.firstElementChild.style == "background-image: url(" + path + "/images/bookmark_border_white_24dp.svg);") {
            postBookmarkButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/bookmark_white_24dp.svg);";
        }
        else if (postBookmarkButtonContainer.firstElementChild.style == "background-image: url(" + path + "/images/bookmark_white_24dp.svg);") {
            postBookmarkButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/bookmark_border_white_24dp.svg);";
        }

        try {
            fetch(path + 'post/' + postId + '/bookmark', {
                method: 'POST',
                headers: {
                    'url': path + 'post/' + postId + '/bookmark',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data === "Bookmarked") {
                    postBookmarkButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/bookmark_white_24dp.svg);";
                }
                else if (data === "Unbookmarked") {
                    postBookmarkButtonContainer.firstElementChild.style = "background-image: url(" + path + "/images/bookmark_border_white_24dp.svg);";
                }
                else if (data === "Login") {
                    window.location.href = path + 'login';
                }
            }).catch(function (error) {
                window.location.href = path + 'login';
            });
        } catch (error) {
            window.location.href = path + 'login';
        }
    }

    postLinkButtonContainer.onclick = function (event) {
        event.preventDefault();

        navigator.clipboard.writeText(path + 'post/' + postId).then(() => alert('Text copied'));

        let flashMessageContainer = document.getElementById("flash-message-container");
        let flashMessageText = document.getElementById("flash-message-text");

        flashMessageContainer.style = "";
        postLinkButtonContainer.blur();
        flashMessageText.innerText = "The link to the post has been copied.";

        clearTimeout(autoHideFlashMessage);

        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style = "display: none;";
            flashMessageText.innerText = "";
        }, 10000);
    }

    postInfoContainer.onclick = function () {
        previewContainer.innerHTML = "";

        previewContainer.style.zIndex = 100;
        document.body.style.overflow = 'hidden';

        let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
        let previewContent = document.createElement("div"); previewContent.setAttribute("class", "preview post-likes-preview-container scrollbar-preview");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";
        let skipFunction = false;

        let previewContentHeading = document.createElement("div"); previewContentHeading.setAttribute("class", "post-likes-preview-heading center-text"); previewContentHeading.innerText = "Likes";

        previewContent.appendChild(previewContentHeading);

        closeButtonContainer.appendChild(closeButton);

        previewBackground.onclick = function () { hidePreview(previewContainer); }
        previewContent.onclick = function () {
            if (!skipFunction) {
                hidePreview(previewContainer);
            }
            else {
                skipFunction = false;
            }
        }

        previewContainer.appendChild(previewBackground);

        closeButtonContainer.onclick = function () { hidePreview(previewContainer); }

        previewContainer.appendChild(closeButtonContainer);

        try {
            fetch(path + 'post/' + postId + '/likesInfo', {
                method: 'POST',
                headers: {
                    'url': path + 'post/' + postId + '/likesInfo',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                usersLiked = data[postId].split("|");

                let usersLikedContainer = document.createElement("div");

                if (usersLiked.length > 1) {
                    for (let i = 0; i < usersLiked.length; i += 2) {
                        let userContainer = document.createElement("div"); userContainer.setAttribute("class", "post-likes-profile-container");
                        let userProfileLink = document.createElement("a"); userProfileLink.setAttribute("href", path + "profile/" + usersLiked[i + 1]);
                        let userProfilePicture = document.createElement("img"); userProfilePicture.setAttribute("class", "post-profile-picture"); userProfilePicture.setAttribute("src", path + "" + (usersLiked[i] != "" ? "storage/profile_pictures/" + usersLiked[i] : "images/person_white_24dp.svg"));
                        let username = document.createElement("a"); username.setAttribute("href", path + "profile/" + usersLiked[i + 1]); username.setAttribute("class", "post-profile-name"); username.innerText = usersLiked[i + 1];

                        userProfileLink.appendChild(userProfilePicture);
                        userContainer.appendChild(userProfileLink);
                        userContainer.appendChild(username);

                        usersLikedContainer.appendChild(userContainer);
                    }
                }

                previewContent.appendChild(usersLikedContainer);

                previewContent.onclick = function() {
                    skipFunction = true;

                    previewContent.onclick;
                }
            }).catch(function (error) {
                return console.log(error);
            });
        } catch (error) {
            return console.log(error);
        }

        previewContainer.appendChild(previewContent);
    }
}

function viewCommentsWindow(path, postId, backButtonPressed) {
    try {
        fetch(path + 'post/' + postId + '/viewComments', {
            method: 'POST',
            headers: {
                'url': path + 'post/' + postId + '/viewComments',
                "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
            }
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (!backButtonPressed) {
                commentsArray.push(postId);
            }

            mainPreviewContainer = document.getElementById('preview');

            mainPreviewContainer.innerHTML = "";

            mainPreviewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let previewContent = document.createElement("div"); previewContent.setAttribute("class", "preview post-comments-preview-container scrollbar-preview");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";
            let backButtonContainer = document.createElement("div"); backButtonContainer.setAttribute("class", "back-button-container");
            let backButton = document.createElement("div"); backButton.setAttribute("class", "back-button"); backButton.style = "background-image: url(" + path + "/images/arrow_back_white_24dp.svg);";
            let skipFunction = false;

            closeButtonContainer.appendChild(closeButton);
            backButtonContainer.appendChild(backButton);

            previewBackground.onclick = function () { 
                hidePreview(mainPreviewContainer); 
                commentsArray = [];
            }
            previewContent.onclick = function () {
                if (!skipFunction) {
                    hidePreview(mainPreviewContainer);
                    commentsArray = [];
                }
                else {
                    skipFunction = false;
                }
            }

            mainPreviewContainer.appendChild(previewBackground);

            closeButtonContainer.onclick = function () { 
                hidePreview(mainPreviewContainer); 
                commentsArray = [];
            }
            backButtonContainer.onclick = function () { 
                commentsArray.pop();

                if (commentsArray.length < 1) {
                    hidePreview(mainPreviewContainer);
                    return;
                }

                viewCommentsWindow(path, commentsArray[commentsArray.length - 1], true);
            }

            mainPreviewContainer.appendChild(closeButtonContainer);
            mainPreviewContainer.appendChild(backButtonContainer);

            previewContent.onclick = function() {
                skipFunction = true;

                previewContent.onclick;
            }

            previewContent.innerHTML = data;

            mainPreviewContainer.appendChild(previewContent);

            eval(getAllFunctionsTextFromHtmlText(data));
        }).catch(function (error) {
            return console.log(error);
        });
    } catch (error) {
        return console.log(error);
    }
}

function showProfilePicturePreview(name, path) {
    let profilePictureContainer = document.getElementById('profile-picture');
    
    let inputFileElement = document.getElementById(name);

    
    inputFileElement.onchange = function() {
        let reader = new FileReader();

        reader.onload = function () {
            if (inputFileElement.files[0].type.match('image.png') || inputFileElement.files[0].type.match('image.jpeg') || inputFileElement.files[0].type.match('image.jpg')) {
                profilePictureContainer.setAttribute("src", reader.result);
            }
            else {
                profilePictureContainer.setAttribute("src", path + "/images/report_problem_white_24dp.svg");
            }
        }

        reader.readAsDataURL(inputFileElement.files[0]);
    }
}

function showBackgroundPicturePreview(name, path) {
    let profileBackgroundPictureContainer = document.getElementById('profile-background');
    
    let inputFileElement = document.getElementById(name);

    inputFileElement.onchange = function() {
        let reader = new FileReader();

        reader.onload = function () {
            if (inputFileElement.files[0].type.match('image.png') || inputFileElement.files[0].type.match('image.jpeg') || inputFileElement.files[0].type.match('image.jpg')) {
                profileBackgroundPictureContainer.setAttribute("src", reader.result);
            }
            else {
                profileBackgroundPictureContainer.setAttribute("src", path + "/images/report_problem_white_24dp.svg");
            }
        }

        reader.readAsDataURL(inputFileElement.files[0]);
    }
}

function setFollowButtonFunctionality(username, numberOfFollowers, path) {
    let profileFollowButton = document.getElementById("profile-" + username);
    let followersInfoContainer = document.getElementById("profile-followers-count");

    profileFollowButton.onclick = function () {
        if (profileFollowButton.innerText === "Following") {
            profileFollowButton.innerText = "Follow";
        }
        else if (profileFollowButton.innerText === "Follow") {
            profileFollowButton.innerText = "Following";
        }

        try {
            fetch(path + 'profile/' + username + '/follow', {
                method: 'POST',
                headers: {
                    'url': path + 'profile/' + username + '/follow',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data === "Followed") {
                    profileFollowButton.innerText = "Following";
                    numberOfFollowers += 1;
                    followersInfoContainer.innerText = numberOfFollowers + " " + (numberOfFollowers === 1 ? "follower" : "followers");
                }
                else if (data === "Unfollowed") {
                    profileFollowButton.innerText = "Follow";
                    numberOfFollowers -= 1;
                    followersInfoContainer.innerText = numberOfFollowers + " " + (numberOfFollowers === 1 ? "follower" : "followers");
                }
                else if (data === "Login") {
                    window.location.href = path + 'login';
                }
                else if (data === "FollowDenied") {
                    console.log("You can't follow yourself. You can't be that sad to do it. Go outside and find some friends.");
                }
            }).catch(function (error) {
                window.location.href = path + 'login';
            });
        } catch (error) {
            window.location.href = path + 'login';
        }
    }
}

function setPreviewFollowersButtonFunctionality(username, path, previewContainer) {
    let followersInfoContainer = document.getElementById("profile-followers-count");

    followersInfoContainer.onclick = function () {
        previewContainer.innerHTML = "";

        previewContainer.style.zIndex = 100;
        document.body.style.overflow = 'hidden';

        let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
        let previewContent = document.createElement("div"); previewContent.setAttribute("class", "preview post-likes-preview-container scrollbar-preview");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style = "background-image: url(" + path + "/images/close_white_24dp.svg);";
        let skipFunction = false;

        let previewContentHeading = document.createElement("div"); previewContentHeading.setAttribute("class", "post-likes-preview-heading center-text"); previewContentHeading.innerText = "Followers";

        previewContent.appendChild(previewContentHeading);

        closeButtonContainer.appendChild(closeButton);

        previewBackground.onclick = function () { hidePreview(previewContainer); }
        previewContent.onclick = function () {
            if (!skipFunction) {
                hidePreview(previewContainer);
            }
            else {
                skipFunction = false;
            }
        }

        previewContainer.appendChild(previewBackground);

        closeButtonContainer.onclick = function () { hidePreview(previewContainer); }

        previewContainer.appendChild(closeButtonContainer);

        try {
            fetch(path + 'profile/' + username + '/followersInfo', {
                method: 'POST',
                headers: {
                    'url': path + 'profile/' + username + '/followersInfo',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                userFollowed = data.split("|");

                let userFollowedContainer = document.createElement("div");

                if (userFollowed.length > 1) {
                    for (let i = 0; i < userFollowed.length; i += 2) {
                        let userContainer = document.createElement("div"); userContainer.setAttribute("class", "post-likes-profile-container");
                        let userProfileLink = document.createElement("a"); userProfileLink.setAttribute("href", path + "profile/" + userFollowed[i + 1]);
                        let userProfilePicture = document.createElement("img"); userProfilePicture.setAttribute("class", "post-profile-picture"); userProfilePicture.setAttribute("src", path + "" + (userFollowed[i] != "" ? "storage/profile_pictures/" + userFollowed[i] : "images/person_white_24dp.svg"));
                        let usernameElement = document.createElement("a"); usernameElement.setAttribute("href", path + "profile/" + userFollowed[i + 1]); usernameElement.setAttribute("class", "post-profile-name"); usernameElement.innerText = userFollowed[i + 1];
                    
                        userProfileLink.appendChild(userProfilePicture);
                        userContainer.appendChild(userProfileLink);
                        userContainer.appendChild(usernameElement);
                    
                        userFollowedContainer.appendChild(userContainer);
                    }
                }

                previewContent.appendChild(userFollowedContainer);

                previewContent.onclick = function() {
                    skipFunction = true;

                    previewContent.onclick;
                }
            }).catch(function (error) {
                return console.log(error);
            });
        } catch (error) {
            return console.log(error);
        }

        previewContainer.appendChild(previewContent);
    }
}

function setFlashMessageCloseButtonFunctionality() {
    let flashMessageContainer = document.getElementById("flash-message-container");
    let flashMessageText = document.getElementById("flash-message-text");
    let flashMessageCloseButton = document.getElementById("flash-message-close-button");

    autoHideFlashMessage = setTimeout(function () {
        flashMessageContainer.style = "display: none;";
        flashMessageText.innerText = "";
    }, 10000);

    if (flashMessageText.innerText == null) {
        clearTimeout(autoHideFlashMessage);
    }

    flashMessageContainer.onchange = function () {
        clearTimeout(autoHideFlashMessage);
        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style = "display: none;";
            flashMessageText.innerText = "";
        }, 10000);
    }

    flashMessageContainer.onmouseenter = function () {
        clearTimeout(autoHideFlashMessage);
    }

    flashMessageContainer.onmouseleave = function () {
        clearTimeout(autoHideFlashMessage);
        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style = "display: none;";
            flashMessageText.innerText = "";
        }, 10000);
    }

    flashMessageCloseButton.onclick = function () {
        clearTimeout(autoHideFlashMessage);
        flashMessageContainer.style = "display: none;";
        flashMessageText.innerText = "";
    }
}

function setRemoveProfilePictureButtonFunctionality(path) {
    let profilePictureRemoveButton = document.getElementById("profile-picture-remove-button");
    let profilePicturePreview = document.getElementById("profile-picture");
    let uploadedProfilePictureFileButton = document.getElementById("uploadedProfilePictureFileLabel");
    let previousPath = profilePicturePreview.src;

    profilePictureRemoveButton.onclick = function () {
        if (profilePictureRemoveButton.checked) {
            previousPath = profilePicturePreview.src;
            profilePicturePreview.setAttribute("src", path + "images/person_white_24dp.svg");
            uploadedProfilePictureFileButton.style = "visibility: hidden;";
        }
        else {
            profilePicturePreview.setAttribute("src", previousPath);
            uploadedProfilePictureFileButton.style = "visibility: visible;"
        }
    }
}

function setRemoveBackgroundPictureButtonFunctionality(path) {
    let backgroundPictureRemoveButton = document.getElementById("background-picture-remove-button");
    let backgroundPicturePreview = document.getElementById("profile-background");
    let uploadedBackgroundPictureFileButton = document.getElementById("uploadedBackgroundPictureFileLabel");
    let previousPath = backgroundPicturePreview.src;

    backgroundPictureRemoveButton.onclick = function () {
        if (backgroundPictureRemoveButton.checked) {
            previousPath = backgroundPicturePreview.src;
            backgroundPicturePreview.setAttribute("src", path + "images/background_default_image.jpg");
            uploadedBackgroundPictureFileButton.style = "visibility: hidden;";
        }
        else {
            backgroundPicturePreview.setAttribute("src", previousPath);
            uploadedBackgroundPictureFileButton.style = "visibility: visible;"
        }
    }
}

function getAllFunctionsTextFromHtmlText(text) {
    let startFunctionRegex = /<script/g;
    let endFunctionRegex = /<\/script/g;
    let result; 
    let startIndexes = [];
    let endIndexes = [];
    let allFunctionsText = "";

    while ( (result = startFunctionRegex.exec(text)) ) {
        startIndexes.push(result.index);
    }

    while ( (result = endFunctionRegex.exec(text)) ) {
        endIndexes.push(result.index);
    }

    if (startIndexes.length === endIndexes.length) {
        for (let i = 0; i < startIndexes.length; i++) {
            allFunctionsText += text.substring(startIndexes[i] + 8, endIndexes[i]).replaceAll(/\s/g, "");
        }
    }

    return allFunctionsText;
}
