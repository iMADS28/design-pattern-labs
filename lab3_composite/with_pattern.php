<?php
// Lab 3 - WITH Composite (GOOD starter, live API, has TODOs)
interface ForumComponent {
    public function display(int $depth = 0): void;
}

class Post implements ForumComponent {
    public function __construct(private string $author, private string $message) {}
    public function display(int $depth = 0): void {
        $indent = str_repeat("  ", $depth);
        echo $indent . "- Post by {$this->author}: {$this->message}\n";
    }
}

// Missing 3: Add a Bundle (pre-set combo) ForumComponent as a new leaf
class Bundle implements ForumComponent {
    public function __construct(private string $title, private array $items) {}
    public function display(int $depth = 0): void {
        $indent = str_repeat("  ", $depth);
        echo $indent . "* Bundle: {$this->title} [" . implode(", ", $this->items) . "]\n";
    }
}

class Thread implements ForumComponent {
    /** @var ForumComponent[] */
    private array $children = [];
    public function __construct(private string $title) {}
    public function add(ForumComponent $c): void { $this->children[] = $c; }
    public function display(int $depth = 0): void {
        // Missing 1: loop over $children and display with $depth+1
        echo str_repeat("  ", $depth) . "+ Thread: {$this->title}\n";
        foreach ($this->children as $child) {
            $child->display($depth + 1);
        }
    }
    public static function fromApi(int $postId): self {
        $postJson = file_get_contents("https://jsonplaceholder.typicode.com/posts/$postId");
        $post = json_decode($postJson, true);
        $thread = new self($post["title"]);
        $thread->add(new Post("Author {$post['userId']}", substr($post["body"], 0, 40) . "..."));
        $commentsJson = file_get_contents("https://jsonplaceholder.typicode.com/posts/$postId/comments");
        $comments = json_decode($commentsJson, true);
        $replies = new Thread("Replies");
        foreach (array_slice($comments, 0, 2) as $c) $replies->add(new Post($c["email"], substr($c["body"], 0, 30) . "..."));
        $thread->add($replies);
        return $thread;
    }
}

// Missing 2: RAG variant: Document/Section/Chunk with getText() and embed()
interface TextComponent {
    public function getText(): string;
    public function embed(): array;
}

class Chunk implements TextComponent {
    public function __construct(private string $text) {}
    public function getText(): string { return $this->text; }
    public function embed(): array {
        return ['chunk' => substr($this->text, 0, 20), 'tokens' => str_word_count($this->text)];
    }
}

class Section implements TextComponent {
    private array $children = [];
    public function __construct(private string $title) {}
    public function add(TextComponent $c): void { $this->children[] = $c; }
    public function getText(): string {
        $out = "## " . $this->title . "\n";
        foreach ($this->children as $child) $out .= $child->getText() . "\n";
        return $out;
    }
    public function embed(): array {
        $res = [];
        foreach ($this->children as $child) $res[] = $child->embed();
        return ['section' => $this->title, 'embeddings' => $res];
    }
}

class Document implements TextComponent {
    private array $children = [];
    public function __construct(private string $title) {}
    public function add(TextComponent $c): void { $this->children[] = $c; }
    public function getText(): string {
        $out = "# " . $this->title . "\n";
        foreach ($this->children as $child) $out .= $child->getText() . "\n";
        return $out;
    }
    public function embed(): array {
        $res = [];
        foreach ($this->children as $child) $res[] = $child->embed();
        return ['document' => $this->title, 'sections' => $res];
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "WITH Composite (GOOD - complete TODOs):\n";
    $thread = Thread::fromApi(1);
    $thread->add(new Bundle("Starter Kit", ["Rules.pdf", "FAQ.txt"]));
    $thread->display();

    echo "\n--- RAG Document Tree Demo ---\n";
    $doc = new Document("AI Architecture Guide");
    $sec = new Section("Introduction");
    $sec->add(new Chunk("Design patterns provide reusable object-oriented solutions."));
    $sec->add(new Chunk("Composite treats leaf and branch objects identically."));
    $doc->add($sec);
    echo $doc->getText();
    echo "Embeddings generated: " . json_encode($doc->embed()) . "\n";
}
