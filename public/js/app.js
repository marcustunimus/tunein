var inputFilesElement;
var uploadedFiles = [];
var removedAttachment = false;
var postFilesLastIndex = 0;
var removedPostFiles = [];

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

function showUploadedFilesPreview(name) {
    let previewContainer = document.getElementById('preview');
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

        for (let i = lastPostPreviewIndex + 1; i < postFilesContainer.childNodes.length;) {
            postFilesContainer.childNodes[i].remove();
        }

        getUploadedFiles(inputFilesElement.files);

        showUploadedFiles(uploadedFiles);
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

function showUploadedFiles(files) {
    let postFilesContainer = document.getElementById('post-files');

    for (let file of files) {
        let reader = new FileReader();
        let filePreview = document.createElement("div"); filePreview.setAttribute("title", file.name); filePreview.setAttribute("style", "z-index: 1;");
        let fileShowcase = document.createElement("figure"); fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
        let fileCaption = document.createElement("figcaption"); fileCaption.innerText = file.name; fileCaption.setAttribute("class", "post-file-upload-caption");
        let imageContainer = document.createElement("img");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-thumbnail-preview-button");

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
            let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button");

            videoContainer.appendChild(playButton);
            fileShowcase.appendChild(videoContainer); fileShowcase.appendChild(fileCaption);
        }
        else if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
            imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", ""); imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");

            fileShowcase.appendChild(imageContainer); fileShowcase.appendChild(fileCaption);
        }
        else {
            let fileContainer = document.createElement("div"); fileContainer.setAttribute("class", "post-file-upload-unknown-file-thumbnail");
            let unknownFile = document.createElement("div"); unknownFile.setAttribute("class", "unknown-file");

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

            loadPreviewFromReaderButton(file, reader, filePreview);
        }

        reader.readAsDataURL(file);
    }
}

function loadPreviewFromReaderButton(file, reader, filePreview) {
    let previewContainer = document.getElementById('preview');

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
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button");

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
                unknownFileContainer.onclick = function () { hidePreview(); }

                previewContainer.appendChild(unknownFileContainer);
            }

            previewContainer.appendChild(closeButtonContainer);

            previewBackground.onclick = function () { hidePreview(); }

            closeButtonContainer.onclick = function () { hidePreview(); }
        }
    }
}

function hidePreview() {
    let previewContainer = document.getElementById('preview');

    document.body.style = "";
    previewContainer.style = "";
    previewContainer.innerHTML = "";
}

function showPostFilesPreview(filesInStringFormat) {
    if (filesInStringFormat === "") {
        return;
    }

    postFilesInStringFormat = filesInStringFormat;

    let filesContainer = document.getElementById('files-input-container');
    let postFilesContainer = document.getElementById('post-files');

    var files = filesInStringFormat.split("|");

    postFilesLastIndex = files.length / 3 - 1;

    // let removedPostFilesElement = document.createElement("input"); removedPostFilesElement.type = "hidden"; removedPostFilesElement.name = "removedPostFiles"; removedPostFilesElement.value = "";
    removedPostFiles = [];

    // filesContainer.appendChild(removedPostFilesElement);

    if (files.length !== 0) {
        for (let i = 0; i < files.length; i += 3) {
            let filePreview = document.createElement("div"); filePreview.setAttribute("title", files[i]); filePreview.setAttribute("style", "z-index: 1;");
            let fileShowcase = document.createElement("figure"); fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
            let fileCaption = document.createElement("figcaption"); fileCaption.innerText = files[i]; fileCaption.setAttribute("class", "post-file-upload-caption");
            let imageContainer = document.createElement("img");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-thumbnail-preview-button");

            closeButtonContainer.onclick = function () {
                removedPostFiles.push(files[i]);
                removedAttachment = true;
                // removedPostFilesElement.value = removedPostFiles.join("/");

                postFilesContainer.removeChild(filePreview);
            }

            if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                let videoContainer = document.createElement("div"); videoContainer.setAttribute("class", "post-file-upload-video-thumbnail");
                let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button");

                videoContainer.appendChild(playButton);
                fileShowcase.appendChild(videoContainer); fileShowcase.appendChild(fileCaption);
            }
            else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]); imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");

                fileShowcase.appendChild(imageContainer); fileShowcase.appendChild(fileCaption);
            }
            else {
                let fileContainer = document.createElement("div"); fileContainer.setAttribute("class", "post-file-upload-unknown-file-thumbnail");
                let unknownFile = document.createElement("div"); unknownFile.setAttribute("class", "unknown-file");

                fileContainer.appendChild(unknownFile);
                fileShowcase.appendChild(fileContainer); fileShowcase.appendChild(fileCaption);
            }

            closeButtonContainer.appendChild(closeButton);
            fileShowcase.appendChild(fileCaption);
            filePreview.appendChild(fileShowcase); filePreview.appendChild(closeButtonContainer);
            postFilesContainer.appendChild(filePreview);

            loadPreviewUploadedPostFileButton(files, i, filePreview);
        }
    }
}

function loadPreviewUploadedPostFileButton(files, i, filePreview) {
    let previewContainer = document.getElementById('preview');

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
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button");

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                let videoContainer = document.createElement("video"); videoContainer.setAttribute("controls", "");
                videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center");
                let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                videoContainer.appendChild(videoSource);
                previewContainer.appendChild(videoContainer);
            }
            else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                let imageContainer = document.createElement("img"); imageContainer.setAttribute("src", "/storage/post_files/" + files[i]); imageContainer.setAttribute("class", "preview center");

                previewContainer.appendChild(imageContainer);
            }
            else {
                let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center");
                unknownFileContainer.innerText = "Unknown File Type"
                unknownFileContainer.onclick = function () { hidePreview(); }

                previewContainer.appendChild(unknownFileContainer);
            }

            previewContainer.appendChild(closeButtonContainer);

            previewBackground.onclick = function () { hidePreview(); }
            closeButtonContainer.onclick = function () { hidePreview(); }
        }
    }
}

function loadPostFiles(postId, filesInStringFormat) {
    if (filesInStringFormat === "") {
        return;
    }

    let previewContainer = document.getElementById('preview');
    let postContentContainer = document.getElementById("postContent" + postId);
    let postFilesContentContainer = document.createElement("div"); postFilesContentContainer.setAttribute("class", "post-files-container");
    let files = filesInStringFormat.split("|");

    if (files.length !== 0) {
        switch (files.length / 3) {
            case 1: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-one");


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-two-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]);
                        imageContainer.setAttribute("class", "post-file-image-two-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.appendChild(postFilesContentContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, previewContainer);
                }

                break;
            };
            case 2: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-one-per-column");


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-two-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]);
                        imageContainer.setAttribute("class", "post-file-image-two-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.appendChild(postFilesContentContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, previewContainer);
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

                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]);

                        if (i === 0) {
                            imageContainer.setAttribute("class", "post-file-image-two-column");
                        }
                        else {
                            imageContainer.setAttribute("class", "post-file-image-one-column");
                        }

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.appendChild(postFilesContentContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, previewContainer);
                }

                break;
            };
            case 4: {
                for (let i = 0; i < files.length; i += 3) {
                    let postFilePreview;

                    postFilePreview = document.createElement("div"); postFilePreview.setAttribute("class", "post-files-content-two-per-row");


                    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", "post-file-video-one-column");
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]);

                        imageContainer.setAttribute("class", "post-file-image-one-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.appendChild(postFilesContentContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, previewContainer);
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
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]);

                        imageContainer.setAttribute("class", "post-file-image-one-column");

                        postFilePreview.appendChild(imageContainer);
                    }

                    postFilesContentContainer.appendChild(postFilePreview);
                    postContentContainer.appendChild(postFilesContentContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, previewContainer);
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
                        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

                        videoContainer.appendChild(videoSource);
                        postFilePreview.appendChild(videoContainer);

                        if (i === 4 * 3) {
                            videoContainer.style = "filter: blur(0.25rem);"

                            let numberOfFilesMoreContainer = document.createElement("div"); numberOfFilesMoreContainer.setAttribute("class", "post-preview-centered-text-container");
                            let numberOfFilesMoreContent = document.createElement("span"); numberOfFilesMoreContent.innerText = "+" + (files.length / 3 - 4);

                            numberOfFilesMoreContainer.appendChild(numberOfFilesMoreContent);
                            postFilePreview.appendChild(numberOfFilesMoreContainer);
                        }
                    }
                    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
                        let imageContainer = document.createElement("img"); imageContainer.id = "peviewImage"; imageContainer.setAttribute("src", "/storage/post_files/" + files[i]);
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
                    postContentContainer.appendChild(postFilesContentContainer);

                    loadPreviewPostFileButton(files, i, postFilePreview, previewContainer);

                    if (i === 4 * 3) {
                        break;
                    }
                }

                break;
            };
        }
    }
}

function loadPreviewPostFileButton(files, i, filePreview) {
    filePreview.setAttribute("class", filePreview.getAttribute('class') + " cursor-pointer");

    filePreview.onclick = function () {
        loadPreviewPostFile(files, i);
    }
}

function loadPreviewPostFile(files, i) {
    let previewContainer = document.getElementById('preview');

    previewContainer.innerHTML = "";

    previewContainer.style.zIndex = 100;
    document.body.style.overflow = 'hidden';

    let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
    let previewContent = document.createElement("div"); previewContent.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;"; previewContent.setAttribute("class", "preview center");
    let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
    let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button");
    let nextButtonContainer = document.createElement("div"); nextButtonContainer.setAttribute("class", i + 3 < files.length ? "next-button-container" : "hidden");
    let nextButton = document.createElement("div"); nextButton.setAttribute("class", "next-button");
    let beforeButtonContainer = document.createElement("div"); beforeButtonContainer.setAttribute("class", i - 3 >= 0 ? "before-button-container" : "hidden");
    let beforeButton = document.createElement("div"); beforeButton.setAttribute("class", "before-button");
    let skipFunction = false;

    closeButtonContainer.appendChild(closeButton);
    nextButtonContainer.appendChild(nextButton);
    beforeButtonContainer.appendChild(beforeButton);

    previewBackground.onclick = function () { hidePreview(); }
    previewContent.onclick = function () {
        if (!skipFunction) {
            hidePreview();
        }
        else {
            skipFunction = false;
        }
    }

    previewContainer.appendChild(previewBackground);

    if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
        let videoContainer = document.createElement("video"); videoContainer.setAttribute("controls", "");
        videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center noselect");
        let videoSource = document.createElement("source"); videoSource.setAttribute("src", "/storage/post_files/" + files[i]); videoSource.setAttribute("type", files[i + 1]);

        videoContainer.appendChild(videoSource);

        videoContainer.onclick = function () {
            skipFunction = true;
        }

        previewContent.appendChild(videoContainer);
        previewContainer.appendChild(previewContent);
    }
    else if (files[i + 1] === 'image/png' || files[i + 1] === 'image/jpeg' || files[i + 1] === 'image/jpg' || files[i + 1] === 'image/gif') {
        let imageContainer = document.createElement("img"); imageContainer.setAttribute("src", "/storage/post_files/" + files[i]); imageContainer.setAttribute("class", "preview center noselect");

        imageContainer.onclick = function () {
            skipFunction = true;
        }

        previewContent.appendChild(imageContainer);
        previewContainer.appendChild(previewContent);
    }
    else {
        let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center noselect");
        unknownFileContainer.innerText = "Unknown File Type"
        unknownFileContainer.onclick = function () { hidePreview(); }

        previewContainer.appendChild(unknownFileContainer);
    }

    closeButtonContainer.onclick = function () { hidePreview(); }
    nextButtonContainer.onclick = function () {
        if (i + 3 < files.length) {
            loadPreviewPostFile(files, i + 3);
        }
    }
    beforeButtonContainer.onclick = function () {
        if (i - 3 >= 0) {
            loadPreviewPostFile(files, i - 3);
        }
    }

    previewContainer.appendChild(closeButtonContainer);
    previewContainer.appendChild(nextButtonContainer);
    previewContainer.appendChild(beforeButtonContainer);
}

function setInteractionButtonsFunctionality(postId, numberOfLikes) {
    let postLikeButtonContainer = document.getElementById("post-" + postId + "-like");
    let postInfoContainer = document.getElementById("post-" + postId + "-info");

    postLikeButtonContainer.onclick = function () {
        fetch('/post/' + postId + '/like', {
            method: 'POST',
            headers: {
                'url': '/post/' + postId + '/like',
                "X-CSRF-Token": document.querySelector('input[name=_token]').value
            }
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (data === "Liked") {
                postLikeButtonContainer.firstElementChild.setAttribute("class", "post-interaction-icon post-liked-icon");
                postLikeButtonContainer.lastElementChild.innerText = "Dislike";
                numberOfLikes += 1;
                postInfoContainer.innerText = numberOfLikes + " likes";
            }
            else if (data === "Disliked") {
                postLikeButtonContainer.firstElementChild.setAttribute("class", "post-interaction-icon post-likable-icon");
                postLikeButtonContainer.lastElementChild.innerText = "Like";
                numberOfLikes -= 1;
                postInfoContainer.innerText = numberOfLikes + " likes";
            }

            return console.log(data);
        }).catch(function (error) {
            return console.log(error);
        });
    }
}