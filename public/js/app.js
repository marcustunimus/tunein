var uploadedFiles = [];
var removedAttachment = false;
var postFilesLastIndex = -1;
var removedPostFiles = [];
var autoHideFlashMessage;
var loadMoreCommentsIndex = 0;
var commentsCount = 3;

function addFilesToForm(elementId, name, inputFilesElement) {
    let postForm = document.getElementById(elementId);

    postForm.onsubmit = function (e) {
        dt = new DataTransfer();

        let filesContainer = document.getElementById('files-input-container');
        let removedPostFilesElement = document.createElement("input"); removedPostFilesElement.type = "hidden"; removedPostFilesElement.name = "removedPostFiles";
        removedPostFilesElement.value = removedPostFiles.join("/");

        filesContainer.appendChild(removedPostFilesElement);

        for (let uploadedFile of uploadedFiles[name]) {
            dt.items.add(uploadedFile);
        }

        inputFilesElement.files = dt.files;
    }
}

function showUploadedFilesPreview(inputFilesElement, path, previewContainer, uploadsContainer, postFilesContainer, name) {
    let uploadsContainerName = uploadsContainer.getAttribute("id");
    let uploadedFilesLabel = document.getElementById(name + "Label");
    let uploadedFilesLabelClone = uploadedFilesLabel.cloneNode(true);

    uploadedFilesLabel.remove();
    
    uploadedFiles[uploadsContainerName] = [];

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

        getUploadedFiles(inputFilesElement.files, uploadsContainerName);

        showUploadedFiles(uploadedFiles[uploadsContainerName], path, previewContainer, postFilesContainer, uploadedFilesLabel);
    }

    postFilesContainer.appendChild(uploadedFilesLabelClone);
}

function getUploadedFiles(files, name) {
    for (let file of files) {
        let duplicate = false;

        duplicate = false;

        for (let uploadedFile of uploadedFiles[name]) {
            if (uploadedFile.name === file.name && uploadedFile.type === file.type && uploadedFile.size === file.size) {
                duplicate = true;

                break;
            }
        }

        if (!duplicate) {
            uploadedFiles[name] = uploadedFiles[name].concat(file);
        }
    }
}

function showUploadedFiles(files, path, previewContainer, postFilesContainer, uploadedFilesLabel) {
    for (let file of files) {
        let reader = new FileReader();
        let filePreview = document.createElement("div"); filePreview.setAttribute("title", file.name); filePreview.setAttribute("style", "z-index: 1;");
        let fileShowcase = document.createElement("figure"); fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
        let fileCaption = document.createElement("figcaption"); fileCaption.innerText = file.name; fileCaption.setAttribute("class", "post-file-upload-caption");
        let imageContainer = document.createElement("img");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-thumbnail-preview-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";

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
            let playButton = document.createElement("div"); playButton.setAttribute("class", "play-movie-button"); playButton.style.backgroundImage = "url(\"" + path + "images/movie_white_24dp.svg\")";

            videoContainer.appendChild(playButton);
            fileShowcase.appendChild(videoContainer); fileShowcase.appendChild(fileCaption);
        }
        else if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
            imageContainer.id = "previewImage"; imageContainer.setAttribute("src", ""); imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");

            fileShowcase.appendChild(imageContainer); fileShowcase.appendChild(fileCaption);
        }
        else {
            let fileContainer = document.createElement("div"); fileContainer.setAttribute("class", "post-file-upload-unknown-file-thumbnail");
            let unknownFile = document.createElement("div"); unknownFile.setAttribute("class", "unknown-file"); unknownFile.style.backgroundImage = "url(\"" + path + "images/report_problem_white_24dp.svg\")";

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

    postFilesContainer.appendChild(uploadedFilesLabel);
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
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (file.type.match('video.mp4') || file.type.match('video.webm')) {
                let videoContainer = document.createElement("div"); videoContainer.setAttribute("class", "preview center"); videoContainer.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;";
                let video = document.createElement("video"); video.setAttribute("controls", ""); video.setAttribute("autoplay", "");
                video.setAttribute(video.width > video.height ? "width" : "height", "100%"); video.setAttribute("class", "preview center");
                let videoSource = document.createElement("source"); videoSource.setAttribute("src", reader.result); videoSource.setAttribute("type", file.type);

                video.appendChild(videoSource);
                videoContainer.appendChild(video);
                previewContainer.appendChild(videoContainer);
            }
            else if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
                let imageContainer = document.createElement("div"); imageContainer.setAttribute("class", "preview center"); imageContainer.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;";
                let image = document.createElement("img"); image.setAttribute("src", reader.result); image.setAttribute("class", "preview center");

                imageContainer.appendChild(image);
                previewContainer.appendChild(imageContainer);
            }
            else {
                let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center"); unknownFileContainer.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;";
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
    loadMoreCommentsIndex = 0;

    if (document.getElementById('preview').innerHTML !== "") {
        document.body.style.overflow = 'hidden';
    }
    else {
        document.body.style = "";

        if ("uploads" in uploadedFiles) {
            let mainPageUploadedFiles = uploadedFiles["uploads"];

            uploadedFiles = [];

            uploadedFiles["uploads"] = mainPageUploadedFiles;
        }
        else {
            uploadedFiles = [];
        }
    }
}

function showPostFilesPreview(files, path, previewContainer, postFilesContainer) {
    if (files === []) {
        return;
    }

    postFilesLastIndex = files.length - 1;

    removedPostFiles = [];

    if (files.length !== 0) {
        for (let i = 0; i < files.length; i += 3) {
            let filePreview = document.createElement("div"); filePreview.setAttribute("title", files[i]['name']); filePreview.setAttribute("style", "z-index: 1;");
            let fileShowcase = document.createElement("figure"); fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
            let fileCaption = document.createElement("figcaption"); fileCaption.innerText = files[i]['name']; fileCaption.setAttribute("class", "post-file-upload-caption");
            let imageContainer = document.createElement("img");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-thumbnail-preview-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";

            closeButtonContainer.onclick = function () {
                removedPostFiles.push(files[i]['name']);
                removedAttachment = true;

                postFilesContainer.removeChild(filePreview);
            }

            if (files[i]['mime_type'] === 'video/mp4' || files[i]['mime_type'] === 'video/webm') {
                let videoContainer = document.createElement("div"); videoContainer.setAttribute("class", "post-file-upload-video-thumbnail");
                let playButton = document.createElement("div"); playButton.setAttribute("class", "play-movie-button"); playButton.style.backgroundImage = "url(\"" + path + "images/movie_white_24dp.svg\")";

                videoContainer.appendChild(playButton);
                fileShowcase.appendChild(videoContainer); fileShowcase.appendChild(fileCaption);
            }
            else if (files[i]['mime_type'] === 'image/png' || files[i]['mime_type'] === 'image/jpeg' || files[i]['mime_type'] === 'image/jpg' || files[i]['mime_type'] === 'image/gif') {
                imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + files[i]['path']); imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");

                fileShowcase.appendChild(imageContainer); fileShowcase.appendChild(fileCaption);
            }
            else {
                let fileContainer = document.createElement("div"); fileContainer.setAttribute("class", "post-file-upload-unknown-file-thumbnail");
                let unknownFile = document.createElement("div"); unknownFile.setAttribute("class", "unknown-file"); unknownFile.style.backgroundImage = "url(\"" + path + "images/report_problem_white_24dp.svg\")";

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

        if (parseInt(files[i]['size']) <= 40 * 1024 * 1024) {
            previewContainer.innerHTML = "";

            previewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (files[i]['mime_type'] === 'video/mp4' || files[i]['mime_type'] === 'video/webm') {
                let videoContainer = document.createElement("div"); videoContainer.setAttribute("class", "preview center"); videoContainer.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;";
                let video = document.createElement("video"); video.setAttribute("controls", ""); video.setAttribute("autoplay", "");
                video.setAttribute(video.width > video.height ? "width" : "height", "100%"); video.setAttribute("class", "preview center");
                let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + files[i]['path']); videoSource.setAttribute("type", files[i]['mime_type']);

                video.appendChild(videoSource);
                videoContainer.appendChild(video);
                previewContainer.appendChild(videoContainer);
            }
            else if (files[i]['mime_type'] === 'image/png' || files[i]['mime_type'] === 'image/jpeg' || files[i]['mime_type'] === 'image/jpg' || files[i]['mime_type'] === 'image/gif') {
                let imageContainer = document.createElement("div"); imageContainer.setAttribute("class", "preview center"); imageContainer.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;";
                let image = document.createElement("img"); image.setAttribute("src", path + files[i]['path']); image.setAttribute("class", "preview center");

                imageContainer.appendChild(image);
                previewContainer.appendChild(imageContainer);
            }
            else {
                let unknownFileContainer = document.createElement("span"); unknownFileContainer.style = "font-size: 3rem;"; unknownFileContainer.setAttribute("class", "preview center"); unknownFileContainer.style = "min-width: 40%; max-width: 40%; min-height: 75%; max-height: 75%;";
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

function loadPostFiles(postId, files, path, previewContainer) {
    if (files.length === 0) {
        return;
    }

    let postContentContainer = document.getElementById("postContent" + postId);
    let postFilesContentContainer = document.createElement("div"); postFilesContentContainer.setAttribute("class", "post-files-container");
    let followingElementContainer = document.getElementById("post-" + postId + "-info-container");
    
    switch (files.length) {
        case 1: {
            for (let i = 0; i < files.length; i++) {
                let postFilePreview = document.createElement("div");
                postFilePreview.setAttribute("class", "post-files-content-one");
                createPostFileElement(files[i], files.length, path, postFilePreview, "post-file-video-two-column", "post-file-image-two-column", false);
                postFilesContentContainer.appendChild(postFilePreview);
                postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);
                loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
            }

            break;
        };
        case 2: {
            for (let i = 0; i < files.length; i++) {
                let postFilePreview = document.createElement("div");
                postFilePreview.setAttribute("class", "post-files-content-one-per-column");
                createPostFileElement(files[i], files.length, path, postFilePreview, "post-file-video-two-column", "post-file-image-two-column", false);
                postFilesContentContainer.appendChild(postFilePreview);
                postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);
                loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
            }

            break;
        };
        case 3: {
            for (let i = 0; i < files.length; i++) {
                let postFilePreview = document.createElement("div");
                postFilePreview.setAttribute("class", i === 0 ? "post-files-content-one-per-column" : "post-files-content-two-per-row");
                createPostFileElement(files[i], files.length, path, postFilePreview, i === 0 ? "post-file-video-two-column" : "post-file-video-one-column", i === 0 ? "post-file-image-two-column" : "post-file-image-one-column", false);
                postFilesContentContainer.appendChild(postFilePreview);
                postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);
                loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
            }

            break;
        };
        case 4: {
            for (let i = 0; i < files.length; i++) {
                let postFilePreview = document.createElement("div");
                postFilePreview.setAttribute("class", "post-files-content-two-per-row");
                createPostFileElement(files[i], files.length, path, postFilePreview, "post-file-video-one-column", "post-file-image-one-column", false);
                postFilesContentContainer.appendChild(postFilePreview);
                postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);
                loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
            }

            break;
        };
        case 5: {
            for (let i = 0; i < files.length; i++) {
                let postFilePreview = document.createElement("div");
                postFilePreview.setAttribute("class", i < 2 ? "post-files-content-two-per-row" : "post-files-content-three-per-row");
                createPostFileElement(files[i], files.length, path, postFilePreview, "post-file-video-one-column", "post-file-image-one-column", false);
                postFilesContentContainer.appendChild(postFilePreview);
                postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);
                loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
            }

            break;
        };
        default: {
            for (let i = 0; i < files.length; i++) {
                let postFilePreview = document.createElement("div");
                postFilePreview.setAttribute("class", i < 2 ? "post-files-content-two-per-row" : "post-files-content-three-per-row");
                createPostFileElement(files[i], files.length, path, postFilePreview, "post-file-video-one-column", "post-file-image-one-column", i === 4 ? true : false);
                postFilesContentContainer.appendChild(postFilePreview);
                postContentContainer.insertBefore(postFilesContentContainer, followingElementContainer);
                loadPreviewPostFileButton(files, i, postFilePreview, path, previewContainer);
                if (i === 4) { break; }
            }

            break;
        };
    }
}

function createPostFileElement(file, numberOfFiles, path, postFilePreview, videoContainerClass, imageContainerClass, lastElement) {
    if (file['mime_type'] === 'video/mp4' || file['mime_type'] === 'video/webm') {
        let videoContainer = document.createElement("video"); videoContainer.setAttribute("class", videoContainerClass);
        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + file['path']); videoSource.setAttribute("type", file['mime_type']);

        videoContainer.appendChild(videoSource);
        postFilePreview.appendChild(videoContainer);

        if (lastElement === true) {
            videoContainer.style = "filter: blur(0.25rem);"

            let numberOfFilesMoreContainer = document.createElement("div"); numberOfFilesMoreContainer.setAttribute("class", "post-preview-centered-text-container");
            let numberOfFilesMoreContent = document.createElement("span"); numberOfFilesMoreContent.innerText = "+" + (numberOfFiles - 4);

            numberOfFilesMoreContainer.appendChild(numberOfFilesMoreContent);
            postFilePreview.appendChild(numberOfFilesMoreContainer);
        }
        else {
            let playButtonContainer = document.createElement("div"); playButtonContainer.setAttribute("class", "center post-file-video-container");
            let playButton = document.createElement("div"); playButton.setAttribute("class", "play-button"); playButton.style.backgroundImage = "url(\"" + path + "images/play_circle_white_24dp.svg\")";

            playButtonContainer.appendChild(playButton);
            postFilePreview.appendChild(playButtonContainer);
        }
    }
    else if (file['mime_type'] === 'image/png' || file['mime_type'] === 'image/jpeg' || file['mime_type'] === 'image/jpg' || file['mime_type'] === 'image/gif') {
        let imageContainer = document.createElement("img"); imageContainer.id = "previewImage"; imageContainer.setAttribute("src", path + file['path']);
        imageContainer.setAttribute("class", imageContainerClass);

        postFilePreview.appendChild(imageContainer);

        if (lastElement === true) {
            imageContainer.style = "filter: blur(0.25rem);"

            let numberOfFilesMoreContainer = document.createElement("div"); numberOfFilesMoreContainer.setAttribute("class", "post-preview-centered-text-container");
            let numberOfFilesMoreContent = document.createElement("span"); numberOfFilesMoreContent.innerText = "+" + (numberOfFiles - 4);

            numberOfFilesMoreContainer.appendChild(numberOfFilesMoreContent);
            postFilePreview.appendChild(numberOfFilesMoreContainer);
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
    let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";
    let nextButtonContainer = document.createElement("div"); nextButtonContainer.setAttribute("class", i + 1 < files.length ? "next-button-container" : "next-button-container-disabled");
    let nextButton = document.createElement("div"); nextButton.setAttribute("class", "next-button"); nextButton.style.backgroundImage = "url(\"" + path + "images/navigate_next_white_24dp.svg\")";
    let beforeButtonContainer = document.createElement("div"); beforeButtonContainer.setAttribute("class", i - 1 >= 0 ? "before-button-container" : "before-button-container-disabled");
    let beforeButton = document.createElement("div"); beforeButton.setAttribute("class", "before-button"); beforeButton.style.backgroundImage = "url(\"" + path + "images/navigate_before_white_24dp.svg\")";
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

    if (files[i]['mime_type'] === 'video/mp4' || files[i]['mime_type'] === 'video/webm') {
        let videoContainer = document.createElement("video"); videoContainer.setAttribute("controls", ""); videoContainer.setAttribute("autoplay", "");
        videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center noselect");
        let videoSource = document.createElement("source"); videoSource.setAttribute("src", path + files[i]['path']); videoSource.setAttribute("type", files[i]['mime_type']);

        videoContainer.appendChild(videoSource);

        videoContainer.onclick = function () {
            skipFunction = true;
        }

        previewContent.appendChild(videoContainer);
        previewContainer.appendChild(previewContent);
    }
    else if (files[i]['mime_type'] === 'image/png' || files[i]['mime_type'] === 'image/jpeg' || files[i]['mime_type'] === 'image/jpg' || files[i]['mime_type'] === 'image/gif') {
        let imageContainer = document.createElement("img"); imageContainer.setAttribute("src", path + files[i]['path']); imageContainer.setAttribute("class", "preview center noselect");

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
        if (i + 1 < files.length) {
            loadPreviewPostFile(files, i + 1, path, previewContainer);
        }
    }
    beforeButtonContainer.onclick = function () {
        if (i - 1 >= 0) {
            loadPreviewPostFile(files, i - 1, path, previewContainer);
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
        try {
            fetch(path + 'posts/' + postId + '/like' + (postLikeButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/favorite_white_24dp.svg\")" ? "/delete" : ""), {
                method: 'POST',
                headers: {
                    'url': path + 'posts/' + postId + '/like' + (postLikeButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/favorite_white_24dp.svg\")" ? "/delete" : ""),
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data === "Liked") {
                    postLikeButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/favorite_white_24dp.svg\")";
                    numberOfLikes += 1;
                    postInfoContainer.innerText = numberOfLikes + " " + (numberOfLikes === 1 ? "like" : "likes");
                }
                else if (data === "Unliked") {
                    postLikeButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/favorite_border_white_24dp.svg\")";
                    numberOfLikes -= 1;
                    postInfoContainer.innerText = numberOfLikes + " " + (numberOfLikes === 1 ? "like" : "likes");
                }
                else if (data === "Login") {
                    window.location.href = path + 'login';
                }
            }).catch(function (error) {
                window.location.href = path + 'login';
            });

            if (postLikeButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/favorite_border_white_24dp.svg\")") {
                postLikeButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/favorite_white_24dp.svg\")";
            }
            else if (postLikeButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/favorite_white_24dp.svg\")") {
                postLikeButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/favorite_border_white_24dp.svg\")";
            }
        } catch (error) {
            window.location.href = path + 'login';
        }
    }

    if (postCommentButtonContainer.getAttribute("class") === "post-interaction-button") {
        postCommentButtonContainer.onclick = function () { viewCommentsWindow(path, postId); }
    }

    postBookmarkButtonContainer.onclick = function () {
        try {
            fetch(path + 'posts/' + postId + '/bookmark' + (postBookmarkButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/bookmark_white_24dp.svg\")" ? "/delete" : "") + "", {
                method: 'POST',
                headers: {
                    'url': path + 'posts/' + postId + '/bookmark' + (postBookmarkButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/bookmark_white_24dp.svg\")" ? "/delete" : "") + "",
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data === "Bookmarked") {
                    postBookmarkButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/bookmark_white_24dp.svg\")";
                }
                else if (data === "Unbookmarked") {
                    postBookmarkButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/bookmark_border_white_24dp.svg\")";
                }
                else if (data === "AttemptToBookmarkComment") {
                    console.log("You can not bookmark comments.");
                }
                else if (data === "Login") {
                    window.location.href = path + 'login';
                }
            }).catch(function (error) {
                window.location.href = path + 'login';
            });

            if (postBookmarkButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/bookmark_border_white_24dp.svg\")") {
                postBookmarkButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/bookmark_white_24dp.svg\")";
            }
            else if (postBookmarkButtonContainer.firstElementChild.style.backgroundImage === "url(\"" + path + "images/bookmark_white_24dp.svg\")") {
                postBookmarkButtonContainer.firstElementChild.style.backgroundImage = "url(\"" + path + "images/bookmark_border_white_24dp.svg\")";
            }
        } catch (error) {
            window.location.href = path + 'login';
        }
    }

    postLinkButtonContainer.onclick = function (event) {
        event.preventDefault();

        navigator.clipboard.writeText(path + 'posts/' + postId).then(() => alert('Text copied'));

        let flashMessageContainer = document.getElementById("flash-message-container");
        let flashMessageText = document.getElementById("flash-message-text");

        flashMessageContainer.style = "";
        postLinkButtonContainer.blur();
        flashMessageText.innerText = "The link to the post has been copied.";

        clearTimeout(autoHideFlashMessage);

        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style.display = "none";
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
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";
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
            fetch(path + 'posts/' + postId + '/likesInfo', {
                method: 'POST',
                headers: {
                    'url': path + 'posts/' + postId + '/likesInfo',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                let usersLikedContainer = document.createElement("div");

                for (let user of data) {
                    let userContainer = document.createElement("div"); userContainer.setAttribute("class", "post-likes-profile-container");
                    let userProfileLink = document.createElement("a"); userProfileLink.setAttribute("href", path + "profile/" + user['username']);
                    let userProfilePicture = document.createElement("img"); userProfilePicture.setAttribute("class", "post-profile-picture"); 
                    userProfilePicture.setAttribute("src", path + "" + (user['profile_picture'] != null ? "storage/profile_pictures/" + user['profile_picture'] : "images/person_white_24dp.svg"));
                    let username = document.createElement("a"); username.setAttribute("href", path + "profile/" + user['username']); username.setAttribute("class", "post-profile-name"); username.innerText = user['username'];

                    userProfileLink.appendChild(userProfilePicture);
                    userContainer.appendChild(userProfileLink);
                    userContainer.appendChild(username);

                    usersLikedContainer.appendChild(userContainer);
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

function setCommentInteractionButtonsFunctionality(postId, numberOfLikes, path, previewContainer) {
    let postLikeButtonContainer = document.getElementById("post-" + postId + "-like");
    let postLinkButtonContainer = document.getElementById("post-" + postId + "-link");
    let postLikeCountElement = document.getElementById("post-" + postId + "-like-count");

    postLikeButtonContainer.onclick = function () {
        try {
            fetch(path + 'posts/' + postId + '/like' + (postLikeButtonContainer.style.backgroundImage === "url(\"" + path + "images/favorite_white_24dp.svg\")" ? "/delete" : ""), {
                method: 'POST',
                headers: {
                    'url': path + 'posts/' + postId + '/like' + (postLikeButtonContainer.style.backgroundImage === "url(\"" + path + "images/favorite_white_24dp.svg\")" ? "/delete" : ""),
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data === "Liked") {
                    postLikeButtonContainer.style.backgroundImage = "url(\"" + path + "images/favorite_white_24dp.svg\")";
                    numberOfLikes += 1;
                    postLikeCountElement.innerText = numberOfLikes;
                }
                else if (data === "Unliked") {
                    postLikeButtonContainer.style.backgroundImage = "url(\"" + path + "images/favorite_border_white_24dp.svg\")";
                    numberOfLikes -= 1;
                    postLikeCountElement.innerText = numberOfLikes;
                }
                else if (data === "Login") {
                    window.location.href = path + 'login';
                }
            }).catch(function (error) {
                window.location.href = path + 'login';
            });

            if (postLikeButtonContainer.style.backgroundImage === "url(\"" + path + "images/favorite_border_white_24dp.svg\")") {
                postLikeButtonContainer.style.backgroundImage = "url(\"" + path + "images/favorite_white_24dp.svg\")";
            }
            else if (postLikeButtonContainer.style.backgroundImage === "url(\"" + path + "images/favorite_white_24dp.svg\")") {
                postLikeButtonContainer.style.backgroundImage = "url(\"" + path + "images/favorite_border_white_24dp.svg\")";
            }
        } catch (error) {
            window.location.href = path + 'login';
        }
    }

    postLinkButtonContainer.onclick = function (event) {
        event.preventDefault();

        navigator.clipboard.writeText(path + 'posts/' + postId).then(() => alert('Text copied'));

        let flashMessageContainer = document.getElementById("flash-message-container");
        let flashMessageText = document.getElementById("flash-message-text");

        flashMessageContainer.style = "";
        postLinkButtonContainer.blur();
        flashMessageText.innerText = "The link to the post has been copied.";

        clearTimeout(autoHideFlashMessage);

        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style.display = "none";
            flashMessageText.innerText = "";
        }, 10000);
    }

    postLikeCountElement.onclick = function () {
        previewContainer.innerHTML = "";

        previewContainer.style.zIndex = 100;
        document.body.style.overflow = 'hidden';

        let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
        let previewContent = document.createElement("div"); previewContent.setAttribute("class", "preview post-likes-preview-container scrollbar-preview");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";
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
            fetch(path + 'posts/' + postId + '/likesInfo', {
                method: 'POST',
                headers: {
                    'url': path + 'posts/' + postId + '/likesInfo',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                let usersLikedContainer = document.createElement("div");

                for (let user of data) {
                    let userContainer = document.createElement("div"); userContainer.setAttribute("class", "post-likes-profile-container");
                    let userProfileLink = document.createElement("a"); userProfileLink.setAttribute("href", path + "profile/" + user['username']);
                    let userProfilePicture = document.createElement("img"); userProfilePicture.setAttribute("class", "post-profile-picture"); 
                    userProfilePicture.setAttribute("src", path + "" + (user['profile_picture'] != null ? "storage/profile_pictures/" + user['profile_picture'] : "images/person_white_24dp.svg"));
                    let username = document.createElement("a"); username.setAttribute("href", path + "profile/" + user['username']); username.setAttribute("class", "post-profile-name"); username.innerText = user['username'];

                    userProfileLink.appendChild(userProfilePicture);
                    userContainer.appendChild(userProfileLink);
                    userContainer.appendChild(username);

                    usersLikedContainer.appendChild(userContainer);
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

function viewCommentsWindow(path, postId, errorKey = "", errorValue = "", bodyText = "") {
    try {
        fetch(path + 'posts/' + postId + '/viewComments?errorKey=' + errorKey + '&errorValue=' + errorValue + '&bodyText=' + bodyText, {
            method: 'POST',
            headers: {
                'url': path + 'posts/' + postId + '/viewComments?errorKey=' + errorKey + '&errorValue=' + errorValue + '&bodyText=' + bodyText,
                "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
            }
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            mainPreviewContainer = document.getElementById('preview');

            mainPreviewContainer.innerHTML = "";

            mainPreviewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let previewContent = document.createElement("div"); previewContent.setAttribute("class", "preview post-comments-preview-container scrollbar-preview");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";
            let skipFunction = false;

            closeButtonContainer.appendChild(closeButton);

            previewBackground.onclick = function () { 
                hidePreview(mainPreviewContainer);
            }
            previewContent.onclick = function () {
                if (!skipFunction) {
                    hidePreview(mainPreviewContainer);
                }
                else {
                    skipFunction = false;
                }
            }

            mainPreviewContainer.appendChild(previewBackground);

            closeButtonContainer.onclick = function () { 
                hidePreview(mainPreviewContainer);
            }

            mainPreviewContainer.appendChild(closeButtonContainer);

            previewContent.onclick = function() {
                skipFunction = true;

                previewContent.onclick;
            }

            previewContent.innerHTML = data[0];

            mainPreviewContainer.appendChild(previewContent);

            eval(getAllFunctionsTextFromHtmlText(data[0]));

            if (data[1] === true) {
                addLoadMoreCommentsButton(postId, path);
            }

            viewCommentsAddOldTextareaInput(data[2], data[3]);

            viewCommentsErrorDisplay(data[4], [data[5]]);
        }).catch(function (error) {
            return console.log(error);
        });
    } catch (error) {
        return console.log(error);
    }
}

function viewCommentsAddOldTextareaInput(name, value) {
    textareaElement = document.getElementById(name);

    textareaElement.innerText = value;
}

function viewCommentsErrorDisplay(name, value) {
    if (name == null || value == null) {
        return;
    }

    container = document.getElementById(name);

    errorElement = document.createElement("div"); errorElement.setAttribute("class", "error");
    errorElement.innerText = value;

    container.appendChild(errorElement);

    container.style.marginBottom = "0px";
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
                profilePictureContainer.setAttribute("src", path + "images/report_problem_white_24dp.svg");
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
                profileBackgroundPictureContainer.setAttribute("src", path + "images/report_problem_white_24dp.svg");
            }
        }

        reader.readAsDataURL(inputFileElement.files[0]);
    }
}

function setFollowButtonFunctionality(username, numberOfFollowers, path) {
    let profileFollowButton = document.getElementById("profile-" + username);
    let followersInfoContainer = document.getElementById("profile-followers-count");

    profileFollowButton.onclick = function () {
        try {
            fetch(path + 'profile/' + username + '/' + (profileFollowButton.innerText === "Following" ? "un" : "") + 'follow', {
                method: 'POST',
                headers: {
                    'url': path + 'profile/' + username + '/' + (profileFollowButton.innerText === "Following" ? "un" : "") + 'follow',
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

            if (profileFollowButton.innerText === "Following") {
                profileFollowButton.innerText = "Follow";
            }
            else if (profileFollowButton.innerText === "Follow") {
                profileFollowButton.innerText = "Following";
            }
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
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";
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
                let userFollowedContainer = document.createElement("div");

                for (let user of data) {
                    let userContainer = document.createElement("div"); userContainer.setAttribute("class", "post-likes-profile-container");
                    let userProfileLink = document.createElement("a"); userProfileLink.setAttribute("href", path + "profile/" + user['username']);
                    let userProfilePicture = document.createElement("img"); userProfilePicture.setAttribute("class", "post-profile-picture"); userProfilePicture.setAttribute("src", path + "" + (user["profile_picture"] != null ? user["profile_picture_path"] : "images/person_white_24dp.svg"));
                    let usernameElement = document.createElement("a"); usernameElement.setAttribute("href", path + "profile/" + user["username"]); usernameElement.setAttribute("class", "post-profile-name"); usernameElement.innerText = user["username"];
                    
                    userProfileLink.appendChild(userProfilePicture);
                    userContainer.appendChild(userProfileLink);
                    userContainer.appendChild(usernameElement);
                    
                    userFollowedContainer.appendChild(userContainer);
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
        flashMessageContainer.style.display = "none";
        flashMessageText.innerText = "";
    }, 10000);

    if (flashMessageText.innerText == null) {
        clearTimeout(autoHideFlashMessage);
    }

    flashMessageContainer.onchange = function () {
        clearTimeout(autoHideFlashMessage);
        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style.display = "none";
            flashMessageText.innerText = "";
        }, 10000);
    }

    flashMessageContainer.onmouseenter = function () {
        clearTimeout(autoHideFlashMessage);
    }

    flashMessageContainer.onmouseleave = function () {
        clearTimeout(autoHideFlashMessage);
        autoHideFlashMessage = setTimeout(function () {
            flashMessageContainer.style.display = "none";
            flashMessageText.innerText = "";
        }, 10000);
    }

    flashMessageCloseButton.onclick = function () {
        clearTimeout(autoHideFlashMessage);
        flashMessageContainer.style.display = "none";
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
            uploadedProfilePictureFileButton.style.visibility = "hidden";
        }
        else {
            profilePicturePreview.setAttribute("src", previousPath);
            uploadedProfilePictureFileButton.style.visibility = "visible"
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
            uploadedBackgroundPictureFileButton.style.visibility = "hidden";
        }
        else {
            backgroundPicturePreview.setAttribute("src", previousPath);
            uploadedBackgroundPictureFileButton.style.visibility = "visible"
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

function autoResizeTextAreas() {
    let textAreaElements = document.getElementsByName("body");
    
    for (let i = 0; i < textAreaElements.length; i++) {
        textAreaElements[i].oninput = function () {
            if (textAreaElements[i].offsetHeight < textAreaElements[i].scrollHeight) {
                let rows = parseInt(textAreaElements[i].getAttribute("rows"), 10);

                if (!isNaN(rows)) {
                    if (rows < 7) {
                        textAreaElements[i].setAttribute("rows", (rows + 1).toString(10));
                    }
                }
            }
        }
    }
}

function deleteFormConfirmationFunctionality(form, previewContainer, path) {
    form.onsubmit = function (e) {
        e.preventDefault();

        previewContainer.innerHTML = "";

        previewContainer.style.zIndex = 100;
        document.body.style.overflow = 'hidden';

        let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
        let previewContent = document.createElement("div"); previewContent.setAttribute("class", "preview confirmation-container scrollbar-preview");
        let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
        let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button"); closeButton.style.backgroundImage = "url(\"" + path + "images/close_white_24dp.svg\")";
        let skipFunction = false;

        closeButtonContainer.appendChild(closeButton);

        previewBackground.onclick = function () { hidePreview(previewContainer); }
        previewContent.onclick = function () {
            skipFunction = true;

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

        let confirmationTextContainer = document.createElement("div"); confirmationTextContainer.setAttribute("class", "confirmation-text text-center"); confirmationTextContainer.innerText = "Do you want to delete this " + form.elements["postType"].value + "?";
        let confirmationButtonsContainer = document.createElement("div"); confirmationButtonsContainer.setAttribute("class", "confirmation-buttons-container");
        let confirmationDeclineButton = document.createElement("div"); confirmationDeclineButton.setAttribute("class", "confirmation-button text-center"); confirmationDeclineButton.innerText = "No";
        let confirmationAcceptButton = document.createElement("div"); confirmationAcceptButton.setAttribute("class", "confirmation-button text-center"); confirmationAcceptButton.innerText = "Yes";

        confirmationDeclineButton.onclick = function () { hidePreview(previewContainer); }

        confirmationAcceptButton.onclick = function () {
            form.submit();
        }

        previewContent.appendChild(confirmationTextContainer);
        confirmationButtonsContainer.appendChild(confirmationDeclineButton);
        confirmationButtonsContainer.appendChild(confirmationAcceptButton);
        previewContent.appendChild(confirmationButtonsContainer);
        previewContainer.appendChild(previewContent);
    }
}

function addLoadMoreCommentsButton(postId, path) {
    let commentsElement = document.getElementById("comments");
    let loadMoreCommentsButtonContainer = document.createElement("div"); loadMoreCommentsButtonContainer.setAttribute("class", "center mt-4");
    let loadMoreCommentsButton = document.createElement("button"); loadMoreCommentsButton.setAttribute("class", "post-comment-load-more-button text-center"); loadMoreCommentsButton.innerText = "Load More...";

    loadMoreCommentsIndex += 1;

    loadMoreCommentsButtonContainer.appendChild(loadMoreCommentsButton);

    loadMoreCommentsButton.onclick = function () {
        loadMoreCommentsButtonFunctionality(postId, path, commentsElement, loadMoreCommentsButtonContainer);
    }

    commentsElement.appendChild(loadMoreCommentsButtonContainer);
}

function loadMoreCommentsButtonFunctionality(postId, path, commentsElement, loadMoreCommentsButtonContainer) {
    loadMoreCommentsIndex += 1;

    loadMoreCommentsButtonContainer.firstChild.disabled = true;

    try {
        fetch(path + 'posts/' + postId + '/viewMoreComments?page=' + loadMoreCommentsIndex, {
            method: 'POST',
            headers: {
                'url': path + 'posts/' + postId + '/viewMoreComments?page=' + loadMoreCommentsIndex,
                "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
            }
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (data[0] === "") {
                console.log("No more comments.");

                return;
            }

            commentsElement.insertAdjacentHTML('beforeend', data[0]);

            eval(getAllFunctionsTextFromHtmlText(data[0]));

            commentsElement.removeChild(loadMoreCommentsButtonContainer);
            loadMoreCommentsButtonContainer.firstChild.disabled = false;

            if (data[1] === true) {
                commentsElement.appendChild(loadMoreCommentsButtonContainer);
            }
        }).catch(function (error) {
            loadMoreCommentsIndex -= 1;

            loadMoreCommentsButtonContainer.firstChild.disabled = false;

            console.log(error);
        });
    } catch (error) {
        console.log(error);
    }
}