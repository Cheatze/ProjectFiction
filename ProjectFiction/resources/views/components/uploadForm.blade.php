<div>
<form action="" method="POST">
    @csrf
    <label for="Title"></label>
    <input type="text" name="title" id="title" placeholder="Story title">   
    <div class="input-group mb-3">
        <label class="input-group-text" for="inputGroupFile01">Upload</label>
        <input type="file" class="form-control" id="inputGroupFile01">
    </div>
    <button type="submit">Submit</button>
</form>
</div>