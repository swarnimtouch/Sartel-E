@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<p>
    Update file columns: <code>msl_code</code>, <code>doctor_name</code>, <code>speciality</code>.
    Only existing MSL records are updated; unmatched MSL rows are skipped.
</p>

<form action="{{ route('doctor.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file">
    <button type="submit">Import</button>
</form>
