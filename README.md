RedkingUploadBundle
=====================

This bundle handle file uploads to Amazon S3 attached to MongoDB documents with the following bundles : 

- [vich/uploader-bundle](https://github.com/dustin10/VichUploaderBundle)
- [knplabs/knp-gaufrette-bundle](https://github.com/KnpLabs/KnpGaufretteBundle)
- [gregwar/image-bundle](https://github.com/Gregwar/ImageBundle)
- and of course [aws/aws-sdk-php](https://github.com/aws/aws-sdk-php)

## Installation

Add bundle to composer.json

```js
{
    "require": {
        "redking/upload-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:redkingteam/redkinguploadbundle.git"
        }
    ]
}
```

Register the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Redking\Bundle\UploadBundle\RedkingUploadBundle(),
        new Vich\UploaderBundle\VichUploaderBundle(),
        new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
        new Gregwar\ImageBundle\GregwarImageBundle(),

    );
}
```

## Configuration

We have to define the AWS S3 creadentials in the configuration : 

```yaml
# app/config/config.yml
redking_upload:
    amazon_s3:
        key: %amazon_s3.key%
        secret: %amazon_s3.secret%
        region: %amazon_s3.region%
        bucket: %amazon_s3.bucket%
    # You can define multiple automatic image resized
    resizes:
        thumb:
            width: 50
        medium:
            width: 250
            height: 250
```

## Usage

When you define VichUploader mappings, use the Amazon S3 service ID 'redking_upload.aws_s3.client'

Exemple of configuration : 

```yaml
# app/config/config.yml
knp_gaufrette:
    stream_wrapper: ~
    adapters:
        smiley_images:
            aws_s3:
                service_id: 'redking_upload.aws_s3.client'
                bucket_name: '%amazon_s3.bucket%'
                options:
                    directory: 'smiley_images'
                    acl: public-read
    filesystems:
        smiley_images_fs:
            adapter:    smiley_images

# Vitch Upload
vich_uploader:
    db_driver: mongodb

    gaufrette: true
    storage:   vich_uploader.storage.gaufrette

    mappings:
        smiley_images:
            uri_prefix:         %amazon_s3.prefix%/smiley_images
            upload_destination: smiley_images_fs
            delete_on_update:   true # should the file be deleted when a new file is uploaded
            delete_on_remove:   true # should the file be deleted when the entity is removed
            namer: redking_upload.namer.object # This namer will replace the filename with the id of the object
        
```
