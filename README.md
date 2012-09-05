# EPUB Converter

This is a CLI application to convert EPUB files for Kobo Touch.

This program is experimental. I don't know correct kepub spec. :-)

This was created to read EPUBs from Tatsu-zine.com <http://tatsu-zine.com/>.

## Requirements

* PHP 5.3 or later

## How to Use

1. Put EPUB files into "files/epub" folder.

2. Run command

	$ php oil r epub:generate_kepub

	kepub files are generated in "files/kepub" folder.

## License

AGPL 3.0. See "LICENSE"
