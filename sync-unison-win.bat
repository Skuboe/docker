    .\unison\windows\2.51.2\unison ./source/ socket://127.0.0.1:5000/ ^
        -auto ^
        -batch ^
        -prefer newer ^
        -path "test/" ^
        -path "trial/" ^
        -path "laravel/" ^
        -ignore "Path .gitkeep" ^
        -ignore "Path test/.git" ^
        -ignore "Path test/.idea" ^
        -ignore "Path test/nbproject" ^
        -ignore "Path test/nbproject/*" ^
        -ignore "Path test/public/storage" ^
        -ignore "Path test/storage/logs" ^
        -ignore "Path trial/.git" ^
        -ignore "Path trial/.idea" ^
        -ignore "Path trial/nbproject" ^
        -ignore "Path trial/nbproject/*" ^
        -ignore "Path trial/public/storage" ^
        -ignore "Path trial/storage/logs" ^
        -ignore "Path laravel/.git" ^
        -ignore "Path laravel/.idea" ^
        -ignore "Path laravel/nbproject" ^
        -ignore "Path laravel/nbproject/*" ^
        -ignore "Path laravel/public/storage" ^
        -ignore "Path laravel/storage/logs"
