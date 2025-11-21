# Changelog

All notable changes to `nalrep/laravel` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2] - 2025-11-20

### Added
- Comprehensive error handling system with custom exceptions
- AI-powered error detection for vague and invalid prompts
- Configurable request timeout for AI agents
- PromptBuilder class for reusable prompt construction
- AI-generated descriptive text for reports
- Table of Contents in README
- Example report screenshot in documentation
- CHANGELOG.md and CONTRIBUTING.md files
- Print-to-PDF functionality for HTML reports

### Changed
- Enforced explicit model configuration (no default models)
- Updated system prompts to include error handling instructions
- Improved error messages for better developer experience
- Enhanced documentation with error handling section
- Removed PDF format in favor of browser's print-to-PDF for HTML reports
- Simplified output formats to HTML and JSON only
- Removed `dompdf/dompdf` dependency

### Fixed
- JSON parsing errors now throw specific exceptions
- Eloquent model to array conversion for better data handling
- Fixed JSON response double-encoding issue
- Improved AI query generation to avoid invalid relationship methods

## [0.1] - 2025-11-19

### Added
- Initial release
- Natural language to SQL query conversion
- Support for OpenAI, OpenRouter, and Ollama
- Safe query execution with read-only enforcement
- Automatic database schema scanning
- Eloquent model integration
- Multiple output formats (HTML, JSON, PDF)
- Blade component for easy integration
- Configurable schema exclusion
- Security validation system
- Frontend input component with example prompts

### Security
- JSON-based query interpreter (no `eval()`)
- Read-only enforcement for all queries
- Schema filtering for sensitive tables
- Static date generation
- Method whitelist validation
