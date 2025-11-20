# Changelog

All notable changes to `nalrep/laravel` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2] - 2025-11-20

### Added
- Comprehensive error handling system with custom exceptions
- AI-powered error detection for vague and invalid prompts
- PDF preview feature with configurable display modes (inline/download)
- Configurable request timeout for AI agents
- PromptBuilder class for reusable prompt construction
- AI-generated descriptive text for reports
- Table of Contents in README
- Example report screenshot in documentation

### Changed
- Enforced explicit model configuration (no default models)
- Updated system prompts to include error handling instructions
- Improved error messages for better developer experience
- Enhanced documentation with error handling section

### Fixed
- Raw PDF bytes display issue (now shows proper preview or download)
- JSON parsing errors now throw specific exceptions

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
