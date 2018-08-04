@section('script')
    <script type="text/javascript">
        $('#title').on('blur', function () {
            var theTitle = this.value.toLowerCase()
                    .trim()
                    .replace(/&/g, '-and-')
                    .replace(/[^a-z0-9-]+/g, '-')
                    .replace(/\-\-/g, '-')
                    .replace(/^-+|-+$/g, ''),
                slugInput = $('#slug');

            slugInput.val(theTitle);
        });
    </script>
@endsection