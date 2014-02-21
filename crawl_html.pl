# I427 Fall 2013, Assignment 2
#   Code authors: Patrick Craig/pjcraig
#                 Patrick Mundy/pmundy
#   based on skeleton code by D Crandall
#
#!/usr/bin/perl
use warnings;
use strict;

# these packages (and others! might be helpful). Check out the documentation online.
require URI;
require HTML::LinkExtor;
require LWP::RobotUA;
require 'spamminess_score.pl';
use POE::Queue::Array;


my $p = HTML::LinkExtor->new;
my $ua = LWP::RobotUA->new('IUB-427-pjcraig', 'pjcraig@indiana.edu');
$ua->delay(0);

# function that takes a URL as a parameter, retrieves that URL from the network, 
# and returns a string containing the HTML contents of the page
#
sub retrieve_page {
  my $url = $_[0];
  
  my $html_string = "";
  
  # Check head for HTML variants
  my $head = $ua->head($url);
    if ($head->content_type eq "text/html") {
      my $response = $ua->get($url);
      if ($response->is_success) {
	    $html_string = $response->decoded_content;    
	  } else {
	    print $response->status_line;
	  }
	  $main::base_url = $response->base;
      return $html_string;
    } else {
	  return 0;
    }
}


# function that takes a string filled with HTML code as a parameter, and then 
# returns a list of that page's hyperlinks (URLs)
#
sub find_links {
    my $html_code = $_[0];
    my @links = ();
    my $link;
    # fill in code here!
    $p->parse($html_code);
    my @links_list = $p->links;
    foreach my $j (@links_list) {
      my @link_ref = @{${j}};
      if ($link_ref[0] eq "a" && $link_ref[1] eq "href") {
        $link = make_absolute_url($link_ref[2], $main::base_url);
        $link = normalize_url($link); 
        push(@links, $link);
      }
    }
    return @links;
}


# function that takes as a parameter the name of a file containing some URLs, one per line, 
#  and returns the set of URLs as a perl list of strings.
#
sub read_urls_file {
    my $filename = $_[0];
   
    die "Can't open file $filename!" unless open(FILE, $filename);
    my @lines=<FILE>;
    my @links=();
    
    foreach my $link (@lines) {
      push(@links, $link)
    }
    close (FILE);
    return @links;
    
}


# function that takes a URL and returns a normalized URL 
#  e.g. each of the following strings:
#
#   http://www.cnn.com/TECH 
#   http://WWW.CNN.COM/TECH/ 
#   http://www.cnn.com/TECH/index.html 
#   http://www.cnn.com/bogus/../TECH/
#
#  would return the following:
#
#   http://www.cnn.com/TECH/

sub normalize_url {
  my $url = $_[0];
  return $url->canonical->as_string;
}


# function that takes a relative or absolute URL and a base URL, and returns an absolute URL
#
#  e.g. make_absolute_url("index.html", "http://www.cnn.com/") should return the string "http://www.cnn.com/index.html"
#       make_absolute_url("gov.html", "http://www.cnn.com/links/index.html") should return the string "http://www.cnn.com/links/gov.html"
#       make_absolute_url("http://www.whitehouse.gov/", "http://www.cnn.com") should return "http://www.whitehouse.gov/"
sub make_absolute_url {
    my $rel_url = $_[0];
    my $bas_url = $_[1];

    my $uri = URI->new_abs($rel_url, $bas_url);
    return $uri;
}

#
# You'll likely need other functions. Add them here!
#


#################################################
# Main program. We expect the user to run the program using one of the following three forms:
#
#   ./crawl.pl seeds_file max_pages output_directory bfs
#   ./crawl.pl seeds_file max_pages output_directory dfs
#   ./crawl.pl seeds_file max_pages output_directory bestfirst known_spam.txt known_notspam.txt
#

# check that the user gave us 4 command line parameters
die "Command line should have at least 4 parameters." unless ($#ARGV+1 > 3);

# fetch first three variables from the command line
my $seeds_file = $ARGV[0];
my $max_pages = $ARGV[1];
my $output_directory = $ARGV[2];
my $known_spam;
my $known_notspam;

my @request_queue;
my %visited = ();
my $parent_spamminess = 0;
my $page_count = 0;
my $spamminess_hash = POE::Queue::Array->new();
my $current_page;
my $file_name;
my $html_code;
our $base_url;

# fetch algorithm from command line
my $algorithm = $ARGV[3];
if ($algorithm =~ /^[bd]fs/) {
    die "dfs/bfs command line should have 4 exactly parameters." unless ($#ARGV+1 == 4);
} elsif ($algorithm eq "bestfirst") {
    die "bestfirst command line should have exactly 6 parameters." unless ($#ARGV+1 == 6);
} else {
    die "Unrecognized algorithm. Fourth parameter should be one of bfs, dfs, or bestfirst."
}

if ($algorithm eq "bestfirst") {
  $known_spam = $ARGV[4];
  $known_notspam = $ARGV[5];
}

# add main body of program here!

@request_queue = read_urls_file($seeds_file);

while (@request_queue > 0 and $page_count < $max_pages) {
  if ($algorithm eq 'bfs') {
    $current_page = shift @request_queue  
  } elsif ($algorithm eq 'dfs') {
    $current_page = pop(@request_queue);
  } else {
    if (defined($spamminess_hash->dequeue_next())) {
      $current_page = $spamminess_hash->dequeue_next(); 
    } else {
      #Nothing in the queue yet... just get the first link and go.
      $current_page = pop(@request_queue);
    }
      
  }
  
  if (exists $visited{$current_page}) {
    print "Visited $current_page already. Moving right along...\n";
  } else {
    chomp($current_page); 
    if (retrieve_page($current_page)) {
	  $html_code = retrieve_page($current_page);
	  print "Now visiting.. $current_page\n";
	
      open(OUTFILE, ">", "$output_directory$page_count.html") or die "Couldn't open: $!";
	  print OUTFILE $html_code;
	  close OUTFILE;
	
      if ($algorithm eq "bestfirst") {
        $parent_spamminess = spamminess_score($known_spam, $known_notspam, "$output_directory$page_count.html");
      }

	  $page_count++;
	  $visited{$current_page}++;
   
      if ($algorithm eq "bestfirst") {
        my @links_list = find_links($html_code);
        foreach my $possible_spam (@links_list) {
          $spamminess_hash->enqueue($parent_spamminess, $possible_spam);
        }
      } else {
	    push(@request_queue, find_links($html_code));
      }
    }
  } 
}

print "Crawl complete!\n";
