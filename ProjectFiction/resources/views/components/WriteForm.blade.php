<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<div class="container">
    {{-- Remove the upload and add a text input with markup --}}
    <form action="" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>

    <div class="mb-3">
        <label for="synopsis" class="form-label">Synopsis</label>
        <textarea class="form-control" id="synopsis" name="synopsis" rows="3" required></textarea>
    </div>

    <div class="mb-3">
        <label for="genre" class="form-label">Genre</label>
        <select class="form-select" id="genre" name="genre" required>
            <option value="">Select a Genre</option>
            <option value="action">action</option>
            <option value="essay">Essay</option>
            <option value="fiction">Fiction</option>
            <option value="fantasy">Fantasy</option>
            <option value="sci-fi">Sci-fi</option>
            <option value="mystery">Mystery</option>
            <option value="sci-fi">Science Fiction</option>
            <option value="mystery">Mystery</option>
            <option value="horror">Horror</option>
            <option value="historical">Historical</option>
            <option value="humor">Humor</option>
            <option value="triller">Triller</option>
            <option value="mythology">Mythology</option>
            <option value="romance">romance</option>
            <option value="biography">Biography</option>
            <option value="supernatural">Supernatural</option>
        </select>
    </div>

    {{-- <div class="mb-3">
        <label for="story_file" class="form-label">Story File</label>
        <input class="form-control" type="file" id="story_file" name="story_file" required>
    </div> --}}
    <div id="editor" style="height: 200px;" name="story">
        <p>Hello World!</p>
    </div>

    <br>

    <button type="submit" class="btn btn-primary">Upload Story</button>


            <!--Validation errors-->
            @if ($errors->any())
                <ul class="">
                    @foreach ($errors->all() as $error)
                        <li class="">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
    </form>
</div>
<!-- Include the Quill library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<!-- Initialize Quill editor -->
<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });
</script>
