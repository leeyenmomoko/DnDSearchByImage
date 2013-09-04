function previewFile(file, type){
    switch(type){
        case 'image/jpeg':
            var reader = new FileReader();
            reader.onload = function (event) {
                var image = new Image();
                var preview = document.createElement('div');
                var progressContainer = document.createElement('div');
                var progress = document.createElement('progress');
                progress.min = 0;
                progress.max = 100;
                progress.value = 0;
                progressContainer.appendChild(progress);
                progressContainer.className = 'progress';
                image.src = event.target.result;
                preview.className = 'preview';
                preview.appendChild(progressContainer);
                preview.appendChild(image);
                fileContainer.appendChild(preview);
                uploadFile(file, progress);
            };
            //console.log(file);
            reader.readAsDataURL(file);
            break;
        case 'text/html':
            var image = new Image();
            var preview = document.createElement('div');
            var progressContainer = document.createElement('div');
            progressContainer.className = 'progress';
            image.src = file;
            preview.className = 'preview';
            preview.appendChild(progressContainer);
            preview.appendChild(image);
            progressContainer.appendChild(document.createTextNode('Ready'));
            fileContainer.appendChild(preview);
            break;
    }
    
}
function readFiles(target){
    //console.log(target);
    var items = target.dataTransfer.files,
        itemscount = target.dataTransfer.files.length,
        storeData = {};
    for(var i = 0; i<itemscount; i++){
        var type = target.dataTransfer.files[i].type;
        switch(type){
            case 'image/jpeg':
                console.log(target.dataTransfer.files[i]);
                previewFile(target.dataTransfer.files[i], type);
                break;
            case 'text/html':
                var data = target.dataTransfer.getData(type);
                console.log(data);
                var src = $('<p></p>').append(data).find('img').attr('src');
                if(src){
                    previewFile(src, type);
                }
                else{
                    alert('image not found.');
                }
                break;
        }
        //var type = target.dataTransfer.types[i];
        //console.log(type);
        //var data = target.dataTransfer.getData(type);
        //console.log(data);
    }
}
function uploadFile(file, progress){
    var formData = tests.formdata ? new FormData() : null;
    if(tests.formdata){
        formData.append('file', file);
    }
    // now post a new XHR request
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php');
    xhr.upload.onprogress = function (event) {
        if(event.lengthComputable){
            var complete = (event.loaded / event.total * 100 | 0);
            progress.value = progress.innerHTML = complete;
        }
    };
    xhr.onload = function (event) {
        if (xhr.status === 200) {
            progress.value = progress.innerHTML = 100;
            setTimeout(function(){
                // when uploaded remove progressbar and show text completed.
                progress.parentNode.appendChild(document.createTextNode(xhr.responseText));
                progress.parentNode.removeChild(progress);
            }, 500);
        }
        else{
            console.log('Something went terribly wrong...');
        }
    };
    
    xhr.send(formData);
}