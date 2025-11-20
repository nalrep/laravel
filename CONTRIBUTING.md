# Contributing to Nalrep

Thank you for considering contributing to Nalrep! We welcome contributions from the community.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Enhancements](#suggesting-enhancements)

## Code of Conduct

This project and everyone participating in it is governed by a code of conduct. By participating, you are expected to uphold this code. Please be respectful and constructive in all interactions.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the issue
- **Expected behavior** vs **actual behavior**
- **Environment details** (Laravel version, PHP version, AI provider)
- **Sample prompts** that trigger the issue
- **Error messages** or logs if available

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Clear title and description** of the feature
- **Use cases** explaining why this would be useful
- **Possible implementation** approach (if you have ideas)
- **Examples** of how the feature would work

### Pull Requests

We actively welcome your pull requests:

1. Fork the repo and create your branch from `main`
2. Make your changes following our coding standards
3. Add tests if applicable
4. Update documentation (README, CHANGELOG)
5. Ensure all tests pass
6. Submit your pull request

## Development Setup

We recommend setting up a development environment that mirrors the structure of this repository. This allows you to test the package within a real Laravel application.

### Project Structure

```
your-project/
├── app/
├── config/
├── database/
├── packages/
│   └── nalrep/           # The package code
│       ├── src/
│       ├── config/
│       ├── resources/
│       ├── routes/
│       └── composer.json
├── composer.json          # Main Laravel project
└── ...
```

### Step-by-Step Setup

1. **Create a new Laravel project**
   ```bash
   composer create-project laravel/laravel nalrep-dev
   cd nalrep-dev
   ```

2. **Create the packages directory**
   ```bash
   mkdir -p packages/nalrep
   ```

3. **Clone or initialize the Nalrep package**
   
   **Option A: Clone the repository**
   ```bash
   cd packages
   git clone https://github.com/nalrep/laravel.git nalrep
   cd ../..
   ```
   
   **Option B: Fork and clone your fork**
   ```bash
   cd packages
   git clone https://github.com/YOUR-USERNAME/laravel.git nalrep
   cd ../..
   ```

4. **Link the package in your main composer.json**
   
   Edit `composer.json` in the root of your Laravel project and add:
   
   ```json
   {
       "repositories": {
           "nalrep/laravel": {
               "type": "path",
               "url": "packages/nalrep"
           }
       },
       "require": {
           "nalrep/laravel": "@dev"
       }
   }
   ```

5. **Install the package**
   ```bash
   composer update nalrep/laravel
   ```

6. **Publish the package configuration**
   ```bash
   php artisan vendor:publish --tag=config --provider="Nalrep\NalrepServiceProvider"
   ```

7. **Configure your environment**
   
   Add to `.env`:
   ```env
   NALREP_DRIVER=openai
   OPENAI_API_KEY=your-api-key-here
   NALREP_OPENAI_MODEL=gpt-4o-mini
   NALREP_TIMEOUT=120
   ```

8. **Set up your database**
   
   Configure your database in `.env` and run migrations:
   ```bash
   php artisan migrate
   ```
   
   Optionally, seed some test data:
   ```bash
   php artisan db:seed
   ```

9. **Test the package**
   
   Add the Nalrep component to a view (e.g., `resources/views/welcome.blade.php`):
   ```blade
   <x-nalrep::input />
   ```
   
   Start the development server:
   ```bash
   php artisan serve
   ```
   
   Visit `http://localhost:8000` and test the natural language reporting!

### Making Changes

1. **Edit package files** in `packages/nalrep/src/`
2. **Changes are immediately reflected** (no need to reinstall)
3. **Test your changes** in the Laravel application
4. **Commit and push** to your fork
5. **Create a pull request** to the main repository

### Autoloading During Development

Since the package is linked via `composer.json`, any changes to PHP files are automatically available. If you add new classes, run:

```bash
composer dump-autoload
```

### Running Tests

```bash
cd packages/nalrep
vendor/bin/phpunit
```

### Debugging Tips

- Enable debug mode: `APP_DEBUG=true` in `.env`
- Check Laravel logs: `storage/logs/laravel.log`
- Use `dd()` or `dump()` for debugging
- Monitor AI responses in the browser console (JSON format)

## Coding Standards

### PHP Standards

- Follow **PSR-12** coding standards
- Use **type hints** for parameters and return types
- Write **descriptive variable and method names**
- Add **PHPDoc blocks** for all classes and public methods
- Keep methods **focused and small** (single responsibility)

### Example:

```php
/**
 * Generate a report from a natural language prompt
 * 
 * @param string $prompt User's natural language query
 * @param string $format Output format (html, json, pdf)
 * @return string Generated report content
 * @throws \Nalrep\Exceptions\NalrepException
 */
public function generate(string $prompt, string $format = 'html'): string
{
    // Implementation
}
```

### Code Organization

- **One class per file**
- **Namespace** must match directory structure
- **Group related functionality** in dedicated directories
- **Use dependency injection** instead of facades in classes

### Testing

- Write tests for new features
- Ensure existing tests pass
- Test edge cases and error conditions
- Use descriptive test method names

```php
/** @test */
public function it_throws_exception_for_vague_prompts()
{
    $this->expectException(VaguePromptException::class);
    
    Nalrep::generate('show me data', 'html');
}
```

## Pull Request Process

1. **Update the CHANGELOG.md** with details of changes under `[Unreleased]`
2. **Update the README.md** if you're adding/changing features
3. **Ensure your code follows** our coding standards
4. **Write clear commit messages**:
   - Use present tense ("Add feature" not "Added feature")
   - Be descriptive but concise
   - Reference issues when applicable

   ```
   Add error handling for vague prompts
   
   - Created VaguePromptException
   - Updated PromptBuilder to instruct AI on error detection
   - Added error handling documentation
   
   Fixes #123
   ```

5. **Your PR will be reviewed** by maintainers
6. **Address any feedback** promptly
7. **Once approved**, your PR will be merged

## Areas We Need Help With

- **Testing**: Writing comprehensive tests
- **Documentation**: Improving examples and guides
- **AI Agents**: Adding support for new AI providers
- **Performance**: Optimizing query execution
- **Security**: Identifying and fixing security issues
- **Examples**: Creating real-world usage examples

## Questions?

Feel free to open an issue with the `question` label if you need help or clarification.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to Nalrep! 🎉
