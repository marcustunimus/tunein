const previewContainer = document.getElementById('preview');
const filesContainer = document.getElementById('files-input-container');
const uploadsContainer = document.getElementById('uploads');

let removedAttachmentsElement = document.createElement("input");
let removed = false;
let removedAttachments = [];

function showUploadedFilesPreview(name) {
    let inputFilesElement = document.getElementById(name);

    removedAttachmentsElement.type = "hidden";
    removedAttachmentsElement.name = "removedAttachments";
    removedAttachmentsElement.value = "";

    filesContainer.appendChild(removedAttachmentsElement);

    var uploadedFiles = [];

    inputFilesElement.onclick = function () {
        inputFilesElement.value = null;
    }

    inputFilesElement.onchange = function () {
        uploadsContainer.innerHTML = "";
        previewContainer.innerHTML = "";

        let newInputFilesElement = inputFilesElement.cloneNode();

        newInputFilesElement.id = "uploadedFiles";
        newInputFilesElement.name = "uploadedFiles[]";

        filesContainer.appendChild(newInputFilesElement);

        for (let removedAttachment of removedAttachments) {
            uploadedFiles = uploadedFiles.filter(file => file.name !== removedAttachment);
        }

        for (let file of inputFilesElement.files) {
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

        for (let uploadedFile of uploadedFiles) {
            removedAttachments = removedAttachments.filter(name => name !== uploadedFile.name);
        }

        removedAttachmentsElement.value = removedAttachments.join("/");

        console.log(uploadedFiles);
        console.log(removedAttachments);

        showUploadedFiles(uploadedFiles);
    }
}

function showUploadedFiles(files) {
    for (let file of files) {
        let reader = new FileReader();
        let filePreview = document.createElement("div");
        let fileShowcase = document.createElement("figure");
        let fileCaption = document.createElement("figcaption");
        let imageContainer = document.createElement("img");
        let closeButtonContainer = document.createElement("div");
        let closeButton = document.createElement("div");

        fileCaption.innerText = file.name;
        fileCaption.setAttribute("class", "post-file-upload-caption");
        fileShowcase.setAttribute("class", "post-file-upload-image-thumbnail-container");
        filePreview.setAttribute("title", file.name);
        filePreview.setAttribute("style", "z-index: 1;");

        closeButtonContainer.setAttribute("class", "close-button-thumbnail-preview-container block");
        closeButtonContainer.onclick = function () {
            removedAttachments.push(file.name);

            removed = true;

            removedAttachmentsElement.value = removedAttachments.join("/");

            uploadsContainer.removeChild(filePreview);
        }

        closeButton.setAttribute("class", "close-thumbnail-preview-button");

        if (file.type.match('video.*')) {
            let videoContainer = document.createElement("div");
            let playButton = document.createElement("div");

            videoContainer.setAttribute("class", "post-file-upload-video-thumbnail");
            playButton.setAttribute("class", "play-button");

            videoContainer.appendChild(playButton);
            fileShowcase.appendChild(videoContainer);
            fileShowcase.appendChild(fileCaption);
        }
        else if (file.type.match('image.*')) {
            imageContainer.id = "peviewImage";
            imageContainer.setAttribute("src", "");
            imageContainer.setAttribute("class", "post-file-upload-image-thumbnail");
            fileShowcase.appendChild(imageContainer);
            fileShowcase.appendChild(fileCaption);
        }

        closeButtonContainer.appendChild(closeButton);
        fileShowcase.appendChild(fileCaption);
        filePreview.appendChild(fileShowcase);
        filePreview.appendChild(closeButtonContainer);
        uploadsContainer.appendChild(filePreview);

        reader.onload = function () {
            if (file.type.match('image.*')) {
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
        if (removed) {
            removed = false;
            return;
        }

        if (file.size <= 20 * 1024 * 1024) {
            previewContainer.innerHTML = "";

            previewContainer.style.zIndex = 100;
            document.body.style.overflow = 'hidden';

            let previewBackground = document.createElement("div");
            let closeButtonContainer = document.createElement("div");
            let closeButton = document.createElement("div");

            previewBackground.setAttribute("class", "block");
            previewBackground.style.position = "absolute";
            previewBackground.style.width = "100%";
            previewBackground.style.height = "100%";
            previewBackground.style.backgroundColor = "rgba(0, 0, 0, 0.5)";

            closeButtonContainer.setAttribute("class", "close-button-container");

            closeButton.setAttribute("class", "close-button");

            closeButtonContainer.appendChild(closeButton);
            previewContainer.appendChild(previewBackground);

            if (file.type.match('image.*')) {
                let imageContainer = document.createElement("img");

                imageContainer.setAttribute("src", reader.result);
                imageContainer.setAttribute("class", "preview center");

                previewContainer.appendChild(imageContainer);
            }
            else if (file.type.match('video.*')) {
                let videoContainer = document.createElement("video");

                videoContainer.setAttribute("src", reader.result);
                videoContainer.setAttribute("controls", "");
                videoContainer.setAttribute(videoContainer.width > videoContainer.height ? "width" : "height", "100%");
                videoContainer.setAttribute("class", "preview center");

                previewContainer.appendChild(videoContainer);
            }

            previewContainer.appendChild(closeButtonContainer);

            previewBackground.onclick = function () {
                hidePreview();
            }

            closeButtonContainer.onclick = function () {
                hidePreview();
            }
        }
    }
}

function hidePreview() {
    document.body.style = "";
    previewContainer.style = "";
    previewContainer.innerHTML = "";
}