# EPUB Converter

This program is a CLI application to convert from EPUB files to KOBO EPUB (kepub) for Kobo Touch.

This is experimental. I don't know correct KOBO EPUB spec, because Rakuten does not open the spec.

This was created to read EPUB files from Tatsu-zine.com http://tatsu-zine.com/.

## Requirements

* PHP 5.3 or later

## How to Use

1. Put EPUB files into "files/epub" folder.

2. Run command

	$ php oil r epub:generate_kepub

	kepub files are generated in "files/kepub" folder.

## License

AGPL 3.0. See "LICENSE"
