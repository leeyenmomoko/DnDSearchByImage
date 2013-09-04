<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>test upload</title>
	<script type="text/javascript">
        var tests = {
            filereader: typeof FileReader != 'undefined',
            dnd: 'draggable' in document.createElement('span'),
            formdata: !!window.FormData,
            progress: "upload" in new XMLHttpRequest,
            
        };
        function removeScript(data){
            var scripts = data.getElementsByTagName('script');
            for(var index = 0; index<scripts; index++){
                scripts[index].innerHTML = '';
            }
            data.onload = '';
            return data;
        }
        function readDropFiles(target, fileContainer){
            if(typeof target.dataTransfer != 'undefined' && 
               typeof target.dataTransfer.files != 'undefined'){
                var items = target.dataTransfer.files;
                var itemsCount = items.length;
                if(itemsCount>0){
                    for(var i = 0; i<itemsCount; i++){
                        var type = items[i].type;
                        switch(type){
                            case 'image/jpeg':
                            case 'image/png':
                            case 'image/gif':
                                if(typeof items[i].getAsFile != 'undefined'){
                                    previewFile(items[i].getAsFile(), "file", fileContainer);
                                }
                                else{
                                    previewFile(items[i], "file", fileContainer);
                                }
                                break;
                        }
                    }
                }
                else{
                    if(typeof target.dataTransfer.items != 'undefined'){
                        itemsCount = target.dataTransfer.items.length;
                    }
                    else if(typeof target.dataTransfer.mozItemCount!= 'undefined'){
                        itemsCount = target.dataTransfer.mozItemCount;
                    }
                    if(itemsCount>0){
                        var data = target.dataTransfer.getData("text/html");
                        var dataObject = document.createElement('div');
                        dataObject.innerHTML = data;
                        var imgs = dataObject.getElementsByTagName('img');
                        var imgsLength = imgs.length
                        if(imgsLength > 0){
                            for(var index = 0; index<imgsLength; index++){
                                dataObject = removeScript(imgs[index]);
                                if(imgs[index].src){
                                    previewFile(imgs[index].src, "dataStr", fileContainer);
                                }
                            }
                        }
                        else{
                            alert('image not found.');
                        }
                    }
                }
            }
            else if(typeof target.files != 'undefined'){
                var items = target.files;
                var itemsCount = items.length;
                if(itemsCount>0){
                    for(var i = 0; i<itemsCount; i++){
                        var type = items[i].type;
                        switch(type){
                            case 'image/jpeg':
                            case 'image/png':
                            case 'image/gif':
                                if(typeof items[i].getAsFile != 'undefined'){
                                    previewFile(items[i].getAsFile(), "file", fileContainer);
                                }
                                else{
                                    previewFile(items[i], "file", fileContainer);
                                }
                                break;
                        }
                    }
                }
            }
            else if(Object.prototype.toString.call(target) == '[object String]'){
                console.log('string');
                previewFile(target, "url", fileContainer);
            }
            else{
                console.log(target.value);
                previewFile(target.value, "file", fileContainer);
                console.log("don't support dataTransfer or files.");
            }
        }
        function uploadFile(file, progress){
            console.log('upload');
            var parent = progress.parentNode;
            var formData = tests.formdata ? new FormData() : null;
            // now post a new XHR request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php');
            xhr.setRequestHeader("Accept","application/json");
            
            if(tests.formdata){
                formData.append('file', file);
            }
            else{
                formData = "file="+encodeURIComponent(file);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            }

            if(tests.progress){
                xhr.upload.onprogress = function (event) {
                    if(event.lengthComputable){
                        var complete = (event.loaded / event.total * 100 | 0);
                        progress.value = progress.innerHTML = complete;
                    }
                };
            }
            xhr.onload = function (event) {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    progress.value = progress.innerHTML = 100;
                    setTimeout(function(){
                        // when uploaded remove progressbar and show text completed.
                        var strEl = document.createElement('div');
                        strEl.className = 'status';
                        strEl.appendChild(document.createTextNode(data.text));
                        parent.appendChild(strEl);
                        if(data.console){
                            console.log(data.console);
                        }
                        if(data.url){
                            var url = data.url;
                            var button = document.createElement('button');
                            button.value = 'Search';
                            //button.target = '_blank';
                            button.appendChild(document.createTextNode('search'));
                            parent.appendChild(button);
                            parent.removeChild(progress);
                            
                            button.onclick = function(event){
                                var formUrl = url;
                                event.preventDefault && event.preventDefault();
                                var formData2 = tests.formdata ? new FormData() : null;
                                var attrs = {'filter' : document.getElementById('dropBox').value};
                                
                                var xhrParser = new XMLHttpRequest();
                                xhrParser.open('POST', 'parse.php');
                                xhrParser.setRequestHeader("Accept","application/json");
                                if(Object.prototype.toString.call(formUrl) == '[object String]'){
                                    formUrl = encodeURIComponent(formUrl);
                                }
                                if(tests.formdata){
                                    formData2.append('url', formUrl);
                                    for(key in attrs){
                                        if(attrs[key] != ''){
                                            formData2.append(key, attrs[key]);
                                        }
                                    }
                                }
                                else{
                                    formData2 = "url="+encodeURIComponent(formUrl);
                                    for(key in attrs){
                                        if(attrs[key] != ''){
                                            formData2 += "&"+key+"="+encodeURIComponent(attrs[key]);
                                        }
                                    }
                                    xhrParser.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                }
                                
                                xhrParser.onload = function (event) {
                                    if (xhrParser.status === 200) {
                                        var parserData = JSON.parse(xhrParser.responseText);
                                        var responseCount = parserData.objs.length;
                                        if(0<responseCount){
                                            if(!document.getElementById('searchResult')){
                                                var searchResultZone = document.createElement('div');
                                                searchResultZone.id = 'searchResult';
                                                document.body.appendChild(searchResultZone);
                                            }
                                            else{
                                                while(document.getElementById('searchResult').hasChildNodes()) {
                                                    document.getElementById('searchResult').removeChild(document.getElementById('searchResult').lastChild);
                                                }
                                            }
                                            for(var index = 0; index<responseCount; index++){
                                                var searchResult = document.getElementById('searchResult');
                                                var searchResultEl = document.createElement('div');
                                                searchResultEl.className = 'searchResultEl';
                                                if(parserData.objs[index].class){
                                                    searchResultEl.className += " "+parserData.objs[index].class;
                                                }
                                                searchResultEl.innerHTML = parserData.objs[index].image;
                                                searchResultEl.innerHTML += parserData.objs[index].link;
                                                searchResult.appendChild(searchResultEl);
                                                
                                            }
                                        }
                                        else{
                                            alert(parserData.console);
                                        }
                                    }
                                }
                                xhrParser.onerror = function (event) {
                                    console.log(event);
                                }
                                xhrParser.send(formData2);
                            }
                            //window.location = url;
                        }
                    }, 500);
                    
                    
                }
                else{
                    console.log('Something went terribly wrong...');
                }
            };
            xhr.onerror = function (event) {
                console.log(event);
                progress.parentNode.appendChild('Failed.');
                progress.parentNode.removeChild(progress);
            }
            xhr.send(formData);
        }

        function previewFile(file, type, fileContainer){
            switch(type){
                case 'file':
                    if(tests.filereader){
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
                            fileContainer.style.display = 'block';
                            uploadFile(file, progress);
                        };
                        reader.readAsDataURL(file);
                    }
                    else{
                        console.log('does not support FileReader.');
                    }
                    break;
                case 'url':
                    var image = new Image();
                    var preview = document.createElement('div');
                    var progressContainer = document.createElement('div');
                    var progress = document.createElement('progress');
                    progress.min = 0;
                    progress.max = 100;
                    progress.value = 0;
                    progressContainer.appendChild(progress);
                    progressContainer.className = 'progress';
                    image.src = file;
                    preview.className = 'preview';
                    preview.appendChild(progressContainer);
                    preview.appendChild(image);
                    fileContainer.appendChild(preview);
                    fileContainer.style.display = 'block';
                    uploadFile(file, progress);
                    //window.location = "https://www.google.com/searchbyimage?&image_url=" + file;
                    break;
                case 'dataStr':
                    var image = new Image();
                    var preview = document.createElement('div');
                    var progressContainer = document.createElement('div');
                    var progress = document.createElement('progress');
                    progress.min = 0;
                    progress.max = 100;
                    progress.value = 0;
                    progressContainer.appendChild(progress);
                    progressContainer.className = 'progress';
                    image.src = file;
                    preview.className = 'preview';
                    preview.appendChild(progressContainer);
                    preview.appendChild(image);
                    fileContainer.appendChild(preview);
                    fileContainer.style.display = 'block';
                    uploadFile(file, progress);
                    break;
            }
        }
        var dropboxprepare = window.onload || function () {};
        window.onload = function(){
			var root = document.createElement('div'),
                inputField = document.createElement('div'),
                source = document.getElementById('dropBox'),
                parent = source.parentNode,
                button = document.createElement('div'),
                normalUpload = document.createElement('div'),
                normalFile = document.createElement('div'),
                normalUrl = document.createElement('div'),
                dropZone = document.createElement('div'),
                tipContainer = document.createElement('div'),
				fileContainer = document.createElement('div');
            if(source){
                inputField.className = 'inputField';
                inputField.style.width = source.offsetWidth + 'px';
                parent.replaceChild(inputField, source);
                inputField.appendChild(source);
                root.appendChild(inputField);
                
                button.className = 'button';
                inputField.appendChild(button);
                
                tipContainer.className = 'tip';
                tipContainer.innerHTML = 'Drop files here.';
                dropZone.appendChild(tipContainer);
                
                dropZone.id = 'dropZone';
                root.appendChild(dropZone);
                
                normalUpload.className = 'normalUpload';
                
                if(tests.filereader){
                    var inputFile = document.createElement('input');
                    inputFile.type = 'file';
                    normalFile.className = 'normalFile';
                    normalFile.innerHTML = '上傳圖片檔案';
                    inputFile.onchange = function(){
                        console.log('inputFile change.');
                        readDropFiles(this, fileContainer);
                        normalUpload.style.display = 'none';
                    };
                    normalFile.appendChild(inputFile);
                }
                
                var inputUrl = document.createElement('input');
                inputUrl.type = 'text';
                var inputUrlSubmit = document.createElement('input');
                inputUrlSubmit.type = 'submit';
                inputUrlSubmit.value = '送出';
                inputUrlSubmit.onclick = function(){
                    readDropFiles(inputUrl.value, fileContainer)
                    normalUpload.style.display = 'none';
                };
                normalUrl.className = 'normalUrl';
                normalUrl.innerHTML = '輸入圖片網址';
                normalUrl.appendChild(inputUrl);
                normalUrl.appendChild(inputUrlSubmit);
                
                normalUpload.appendChild(normalFile);
                normalUpload.appendChild(normalUrl);
                root.appendChild(normalUpload);
                
                fileContainer.className = 'fileContainer';
                root.appendChild(fileContainer);
                
                root.className = 'dropBox';
                parent.appendChild(root);

                var dropped = false;
                
                if(tests.dnd){
                    document.ondragenter = function(){
                        dropZone.style.display = "block";
                        normalUpload.style.display = 'none';
                        return false;
                    };
                    document.onmouseover = function(){
                        if(dropped === false){
                            dropZone.style.display = "none";
                        }
                    };
                    dropZone.ondragover = function(){
                        this.className = 'hover';
                        dropped = true;
                        return false;
                    };
                    dropZone.ondragleave = function(){
                        this.className = '';
                        dropped = false;
                        return false;
                    };
                    dropZone.ondrop = function (event) {
                        event.preventDefault && event.preventDefault();
                        
                        //remove dropZone's class 
                        this.className = '';
                        this.style.display = 'none';
                        readDropFiles(event, fileContainer);
                        /*document.onmouseover = function(){
                        };*/
                        return false;
                    };
                }
                else{
                    console.log("don't support dnd");
                }
                button.onclick = function(){
                    if(normalUpload.style.display == 'inline-block'){
                        normalUpload.style.display = 'none';
                    }
                    else{
                        normalUpload.style.display = 'inline-block';
                        dropZone.style.display = 'none';
                    }
                };
                
                
            }
		};
	</script>
	<style type="text/css">
        .dropBox > div{
            margin: 5px 0px;
        }
        .inputField{
            position: relative;
        }
        .inputField #dropBox{
            color: #999999;
        }
        .inputField .button{
            top: 50%;
            right: 0%;
            margin-top: -6px;
            width: 18px;
            height: 13px;
            position: absolute;
            background: url('images/camera.gif') no-repeat right center;
            cursor: pointer;
        }
        .dropBox .normalUpload div{
            padding: 5px;
        }
		#dropZone{
			border: 5px dashed #C6C6C6;
			color: #636363;
			font-size: 150%;
			width: 800px;
			min-height: 50px;
			padding: 10px;
            display: none;
		}
		#dropZone.hover{
			border-color: #45FF45;
		}
		#dropZone .tip{
			padding: 25px;
			text-align: center;
		}
        .normalUpload{
            border: 5px dashed #C6C6C6;
			color: #636363;
            display: none;
            padding: 10px;
        }
        .fileContainer{
            border: 5px solid #C6C6C6;
			color: #636363;
			font-size: 150%;
			width: 800px;
			min-height: 50px;
			padding: 10px;
            display: none;
        }
		.fileContainer .preview{
			position: relative;
			width: 25%;
			display: inline-block;
			text-align: center;
		}
		.fileContainer .preview img{
			width: 100%;
		}
		.fileContainer .preview .progress{
			position: absolute;
			width: 100%;
			text-align: center;
			bottom: 10px;
			font-size: 75%;
            background: rgba(255, 255, 255, 0.5);
		}
		.fileContainer .preview .progress progress{
			width: 80%;
		}
        #searchResult{
            border: 5px solid #C6C6C6;
            width: 800px;
			min-height: 50px;
			padding: 10px;
        }
        #searchResult .searchResultEl.match{
            
        }
        #searchResult .searchResultEl.noMatch{
            opacity: 0.4;
        }
	</style>
</head>
<body>
    <div class="upload"></div>
    <input name="search" id="dropBox" value="books.com.tw" />
</body>
</html>