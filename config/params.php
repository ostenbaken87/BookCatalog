<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'smsPilotApiKey' => 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ', // Ключ эмулятор для тестирования
    
    // Pagination settings
    'pagination' => [
        'booksPageSize' => 20,
        'authorsPageSize' => 20,
        'authorBooksPageSize' => 10,
        'topAuthorsLimit' => 10,
    ],
    
    // Upload paths
    'uploadPaths' => [
        'books' => '/uploads/books',
    ],
];
