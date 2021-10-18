var postFilesContainer = document.getElementById('post-files');

let removedPostFile = false;
let removedPostFiles = [];

function showPostFilesPreview(filesInStringFormat) {
    var files = filesInStringFormat.split("|");

    let removedPostFilesElement = document.createElement("input"); removedPostFilesElement.type = "hidden"; removedPostFilesElement.name = "removedPostFiles"; removedPostFilesElement.value = "";

    postFilesContainer.appendChild(removedPostFilesElement);

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
                removedPostFile = true;
                removedPostFilesElement.value = removedPostFiles.join("/");

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

            loadPreviewPostFileButton(files, i, filePreview);
        }
    }
}

function loadPreviewPostFileButton(files, i, filePreview) {
    filePreview.setAttribute("class", "cursor-pointer");

    filePreview.onclick = function () {
        if (removedPostFile) {
            removedPostFile = false;
            return;
        }

        if (parseInt(files[i + 2]) <= 20 * 1024 * 1024) {
            previewContainer.innerHTML = "";

            previewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button");

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (files[i + 1] === 'video/mp4' || files[i + 1] === 'video/webm') {
                let videoContainer = document.createElement("video"); videoContainer.setAttribute("src", "/storage/post_files/" + files[i]); videoContainer.setAttribute("controls", "");
                videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center");

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