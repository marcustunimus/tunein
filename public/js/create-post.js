const previewContainer = document.getElementById('preview');
const filesContainer = document.getElementById('files-input-container');
const uploadsContainer = document.getElementById('uploads');

var inputFilesElement;
var uploadedFiles = [];

let removedAttachment = false;

function addFilesToForm(elementId) {
    let postForm = document.getElementById(elementId);
    postForm.onsubmit = function (e) {
        dt = new DataTransfer();

        for (let uploadedFile of uploadedFiles) {
            dt.items.add(uploadedFile);
        }

        inputFilesElement.files = dt.files;
    }
}

function showUploadedFilesPreview(name) {
    inputFilesElement = document.getElementById(name);

    inputFilesElement.onclick = function () {
        inputFilesElement.value = null;
    }

    inputFilesElement.onchange = function () {
        uploadsContainer.innerHTML = "";
        previewContainer.innerHTML = "";

        if (typeof postFilesContainer !== 'undefined') {
            if (postFilesContainer.contains(div)) {
                uploadsContainer.appendChild(postFilesContainer);
            }
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

            uploadsContainer.removeChild(filePreview);
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
        uploadsContainer.appendChild(filePreview);

        reader.onload = function () {
            if (file.type.match('image.png') || file.type.match('image.jpeg') || file.type.match('image.jpg') || file.type.match('image.gif')) {
                imageContainer.setAttribute("src", reader.result);
            }

            loadPreviewButton(file, reader, filePreview);
        }

        reader.readAsDataURL(file);
    }
}

function loadPreviewButton(file, reader, filePreview) {
    filePreview.setAttribute("class", "cursor-pointer");

    filePreview.onclick = function () {
        if (removedAttachment) {
            removedAttachment = false;
            return;
        }

        if (file.size <= 20 * 1024 * 1024) {
            previewContainer.innerHTML = "";

            previewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div"); previewBackground.setAttribute("class", "preview-background block");
            let closeButtonContainer = document.createElement("div"); closeButtonContainer.setAttribute("class", "close-button-container");
            let closeButton = document.createElement("div"); closeButton.setAttribute("class", "close-button");

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (file.type.match('video.mp4') || file.type.match('video.webm')) {
                let videoContainer = document.createElement("video"); videoContainer.setAttribute("src", reader.result); videoContainer.setAttribute("controls", "");
                videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%"); videoContainer.setAttribute("class", "preview center");

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
    document.body.style = "";
    previewContainer.style = "";
    previewContainer.innerHTML = "";
}