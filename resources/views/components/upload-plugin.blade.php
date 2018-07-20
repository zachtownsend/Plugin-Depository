<form action="{{ route('upload-plugin') }}" method="POST" enctype="multipart/form-data" class="upload-plugin">
    {{ csrf_field() }}
    <h2>Upload plugin file</h2>
    <div class="form-group">
        <label for="plugin">Plugin zip file</label>
        <input type="file" class="form-control-file" name="plugin" id="plugin">
    </div>
    <button type="submit" class="btn btn-primary">Add Plugin</button>
</form>
