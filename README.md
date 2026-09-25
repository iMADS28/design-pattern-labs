# Structural Design Patterns in Web Application Development

- **Student Name:** [Student Name / Group Members]
- **Section:** [Class Section]
- **Course:** Integrative Programming and Technologies (IPT)
- **Institution:** College of Computing Studies
- **Laboratory Activity:** Web / Software Engineering — Structural Design Patterns (PHP 8)
- **Date:** September 25, 2026

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Folder Structure](#folder-structure)
3. [Labs Summary & Execution](#labs-summary--execution)
   - [Lab 1 — Adapter Pattern](#lab-1--adapter-pattern-incompatible-file-writers)
   - [Lab 2 — Bridge Pattern](#lab-2--bridge-pattern-report-formatter--data-source--compression)
   - [Lab 3 — Composite Pattern](#lab-3--composite-pattern-forum-tree--rag-document-tree)
   - [Lab 4 — Decorator Pattern](#lab-4--decorator-pattern-http-middleware-pipeline)
4. [References](#references)

---

## Project Overview

This repository contains the complete implementation, UML diagrams, and architectural reflections for the four fundamental Gang of Four (GoF) structural design patterns applied to real-world web application engineering:
- **Adapter:** Unifying incompatible third-party file writers behind a single interface.
- **Bridge:** Decoupling report format abstractions from data source and compression implementations to prevent Cartesian class explosion.
- **Composite:** Rendering hierarchical part-whole trees (forum threads and RAG document chunks) uniformly without type-checking branches.
- **Decorator:** Building dynamic HTTP middleware pipelines without subclass explosion.

All code is written in clean, modern PHP 8 complying with PSR-12 conventions.

---

## Folder Structure

```text
design-patterns-labs/
|-- README.md
|-- .gitignore
|-- lab1_adapter/
|   |-- without_web.php          # BAD version (format branching)
|   |-- with_pattern.php         # GOOD version (Adapter pattern + 4th XML adapter)
|   |-- lab1_uml.png             # UML diagram
|   |-- lab1.puml                # PlantUML source
|   |-- reflection.pdf           # 1-page architectural reflection (PDF)
|   `-- reflection.md            # 1-page architectural reflection (Markdown)
|-- lab2_bridge/
|   |-- without_web.php          # BAD version (class explosion)
|   |-- with_pattern.php         # GOOD version (Bridge pattern: 2 sources x 2 compressors)
|   |-- data.json                # Local data source payload for offline execution
|   |-- lab2_uml.png             # UML diagram
|   |-- lab2.puml                # PlantUML source
|   |-- reflection.pdf           # 1-page architectural reflection (PDF)
|   `-- reflection.md            # 1-page architectural reflection (Markdown)
|-- lab3_composite/
|   |-- without_web.php          # BAD version (instanceof branching)
|   |-- with_pattern.php         # GOOD version (Composite: Forum tree + RAG tree)
|   |-- lab3_uml.png             # UML diagram
|   |-- lab3.puml                # PlantUML source
|   |-- reflection.pdf           # 1-page architectural reflection (PDF)
|   `-- reflection.md            # 1-page architectural reflection (Markdown)
`-- lab4_decorator/
    |-- without_web.php          # BAD version (pipeline inheritance explosion)
    |-- with_pattern.php         # GOOD version (Decorator: HTTP middleware pipeline)
    |-- lab4_uml.png             # UML diagram
    |-- lab4.puml                # PlantUML source
    |-- reflection.pdf           # 1-page architectural reflection (PDF)
    `-- reflection.md            # 1-page architectural reflection (Markdown)
```

---

## Labs Summary & Execution

### Lab 1 — Adapter Pattern: Incompatible File Writers

- **Problem:** Attendance export controller branches across CSV, JSON, and text writers.
- **Solution:** `FileWriter` target interface with `CsvFileAdapter`, `JsonFileAdapter`, `TextFileAdapter`, and `XmlWriterAdapter`.
- **Run Commands:**
  ```bash
  cd lab1_adapter
  php without_web.php
  php with_pattern.php
  ```

---

### Lab 2 — Bridge Pattern: Report Formatter × Data Source × Compression

- **Problem:** Combinatorial subclass explosion when combining report formats, data sources, and compression modes ($M \times N \times K$).
- **Solution:** `TimesFormatter` abstraction delegates to `DataSource` (`ApiDataSource`, `FileDataSource`) and `Compressor` (`NoneCompressor`, `GzipCompressor`). Includes runtime compressor switching via `setCompressor()`.
- **Run Commands:**
  ```bash
  cd lab2_bridge
  php without_web.php
  php with_pattern.php
  ```

---

### Lab 3 — Composite Pattern: Forum Tree + RAG Document Tree

- **Problem:** Forum tree traversal requires procedural `instanceof` checks that break when new node types are introduced.
- **Solution:** `ForumComponent` interface implemented uniformly by leaf `Post`, extensibility leaf `Bundle`, and composite `Thread`. Includes RAG twin (`Document` &rarr; `Section` &rarr; `Chunk`) with `getText()` and `embed()`.
- **Run Commands:**
  ```bash
  cd lab3_composite
  php without_web.php
  php with_pattern.php
  ```

---

### Lab 4 — Decorator Pattern: HTTP Middleware Pipeline

- **Problem:** Stacking cross-cutting concerns (Logging, Caching, Retry, Token Counting) requires $2^n = 16$ subclasses.
- **Solution:** `HttpDecorator` wrapping `HttpClient` dynamically at runtime. Demonstrates pipeline stacking and the critical impact of decorator execution order.
- **Run Commands:**
  ```bash
  cd lab4_decorator
  php without_web.php
  php with_pattern.php
  ```

---

## References

- Gamma, E., Helm, R., Johnson, R., & Vlissides, J. (1994). *Design Patterns: Elements of Reusable Object-Oriented Software*. Addison-Wesley.
- Buschmann, F., Meunier, R., Rohnert, H., Sommerlad, P., & Stal, M. (1996). *Pattern-Oriented Software Architecture: A System of Patterns*. Wiley.
- Martin, R. C. (2003). *Agile Software Development, Principles, Patterns, and Practices*. Prentice Hall.
