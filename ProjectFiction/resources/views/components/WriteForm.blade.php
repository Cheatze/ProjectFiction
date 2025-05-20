<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<div class="container">
    {{-- Remove the upload and add a text input with markup --}}
    <form action="{{ route('write') }}" method="POST" enctype="multipart/form-data" onsubmit="setStory()">
    @csrf

    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" required value="{{ old('title') }}">
    </div>

    <div class="mb-3">
        <label for="synopsis" class="form-label">Synopsis</label>
        <textarea class="form-control" id="synopsis" name="synopsis" rows="3" required>{{ old('synopsis') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="genre" class="form-label">Genre</label>
        <select class="form-select" id="genre" name="genre" required>
            <option value="">Select a Genre</option>
            <option value="Action">Action</option>
            <option value="Essay">Essay</option>
            <option value="Fiction">Fiction</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Mystery">Mystery</option>
            <option value="Science Fiction">Science Fiction</option>
            <option value="Horror">Horror</option>
            <option value="Historical">Historical</option>
            <option value="Humor">Humor</option>
            <option value="Thriller">Thriller</option>
            <option value="Mythology">Mythology</option>
            <option value="Romance">romance</option>
            <option value="Biography">Biography</option>
            <option value="Supernatural">Supernatural</option>
        </select>
    </div>

    <div id="editor" style="height: 200px;">
        {!! old('story') !!} {{-- Important: Use !! !! for HTML content --}}
    </div>
    <textarea name="story" id="story-content" style="display: none;"></textarea>

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

        var quill = new Quill('#editor', {
            theme: 'snow'
        });

        // const form = document.querySelector('form');
        const storyContent = document.querySelector('#story-content');

        // form.addEventListener('submit', function (event) {
        //     storyContent.value = quill.root.innerHTML;
        // });

        // const oldStoryContent = document.querySelector('#story-content').value;
        // quill.root.innerHTML = oldStoryContent;


        function setStory(){
            storyContent.value = quill.root.innerHTML;
        }

</script>
