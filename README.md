## AWS S3 Setup (Uploads and Viewing)

This application can store banners and PDFs privately in AWS S3 and view them through presigned URLs.

### 1) Bucket
- Create a bucket named `filestoredintel` in region `us-east-1`.
- Keep “Block all public access” enabled.
- Set Object Ownership to “Bucket owner enforced (ACLs disabled)”.
- No website hosting is required.

### 2) IAM User Policy
Attach the following inline policy to the IAM user whose access keys are configured in `initialize.php`:

```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "BucketMeta",
      "Effect": "Allow",
      "Action": ["s3:GetBucketLocation", "s3:ListBucket"],
      "Resource": "arn:aws:s3:::filestoredintel"
    },
    {
      "Sid": "ObjectsUnderFilesPrefix",
      "Effect": "Allow",
      "Action": ["s3:PutObject", "s3:GetObject"],
      "Resource": "arn:aws:s3:::filestoredintel/Files/*"
    }
  ]
}
```

### 3) App Configuration
`initialize.php` reads environment variables when present; otherwise it uses defaults:
- `AWS_S3_ENABLE = true`
- `AWS_S3_BUCKET = filestoredintel`
- `AWS_S3_REGION = us-east-1`
- `AWS_S3_BASE_PREFIX = Files/`
- `AWS_S3_REQUIRE = true` (treat S3 failures as errors)
- `AWS_S3_USE_SDK = true`

Restart Apache after changes.

### 4) Test Connectivity
Run the connectivity test script to confirm uploads and presigned URLs work:

```
C:\xampp\php\php.exe tools\test_s3.php
```

On success, the script uploads `Files/diagnostics/connectivity.txt` and prints a presigned URL. It also logs to `uploads/logs/s3_upload.log`.

### 4.1) Test PDF Connectivity
To specifically verify PDF uploads and viewing via presigned URLs:

```
C:\xampp\php\php.exe tools\test_s3_document.php
```

What it does:
- Builds a tiny one-page PDF in memory.
- Uploads it to `Files/diagnostics/test-document-<timestamp>.pdf` with content type `application/pdf`.
- If `AWS_S3_USE_SDK = true`, prints a presigned URL valid for 15 minutes; otherwise prints the object URL.
- Logs success or errors to `uploads/logs/s3_upload.log`.

If you get “Bucket does not exist” or “Access Denied”, recheck:
- Bucket name/region in `initialize.php`.
- IAM policy includes: `s3:GetBucketLocation`, `s3:ListBucket` for the bucket; `s3:PutObject`, `s3:GetObject` for `filestoredintel/Files/*`.
- “Block Public Access” can stay enabled; viewing uses presigned URLs.

### 5) Optional CORS
Presigned GETs generally don’t need CORS. If you plan browser XHR/fetch to S3, add a permissive CORS rule:

```
[
  {
    "AllowedHeaders": ["*"],
    "AllowedMethods": ["GET", "HEAD"],
    "AllowedOrigins": ["*"],
    "ExposeHeaders": ["ETag", "Content-Length", "Content-Range"],
    "MaxAgeSeconds": 3000
  }
]
```

### 6) Troubleshooting
- If you see `PermanentRedirect` or “wrong region”, verify the bucket is `us-east-1` and the IAM policy includes `s3:GetBucketLocation`.
- If uploads fail with `BlockPublicAcls`, ensure the app does not request public ACLs (this code uploads private objects and uses presigned URLs).
- Ensure PHP limits are large enough for your PDFs: update `upload_max_filesize` and `post_max_size` in `php.ini`.
# pupbinanonlinerepo