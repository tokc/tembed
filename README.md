# Tembed
![Screenshot one.](https://raw.githubusercontent.com/tokc/tokc.github.io/master/tembed1.png)

Tembed is a quick and dirty Wordpress custom post type put together to create a paginated archive of images that happened to be hosted on Twitter.

![Screenshot two.](https://raw.githubusercontent.com/tokc/tokc.github.io/master/tembed2.png)

I paste the URL of a tweet into the base URL field, hit publish, and the script scrapes the tweet page for an image. The URL is stored in a table along with a scraped timestamp to keep images chronological.

![Screenshot three.](https://raw.githubusercontent.com/tokc/tokc.github.io/master/tembed3.png)

The result is a paginated archive of thumbnails that can be clicked to browse to the corresponding tweet on Twitter.

![Screenshot four.](https://raw.githubusercontent.com/tokc/tokc.github.io/master/tembed4.jpg)

## Fun issues.
If the tweet being scraped is a reply to another tweet with an image, then the wrong post date will be scraped from the parent tweet. This doesn't bother me, so I haven't fixed it, but here's your FYI.