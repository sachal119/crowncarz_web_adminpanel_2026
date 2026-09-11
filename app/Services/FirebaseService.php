<?php

namespace App\Services;

use Kreait\Firebase\Factory;

use Kreait\Firebase\Database;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Firestore;
use Kreait\Firebase\Storage;

class FirebaseService
{
    protected $firebase;

    protected $database;
    
    protected $messaging;
    
    protected $storage;

    public function __construct()
    {
        // $factory = (new Factory)
        //     ->withServiceAccount(storage_path('app/firebase_credentials.json'))
        //     ->withDatabaseUri('https://crown-carz-default-rtdb.firebaseio.com/');

        // $this->database = $factory->createDatabase();
        
        $this->firebase = (new Factory)
            ->withServiceAccount(storage_path('app/firebase_credentials.json'))
            ->withDatabaseUri('https://crown-carz-default-rtdb.firebaseio.com/')
            ->withDefaultStorageBucket('crown-carz.firebasestorage.app'); // ✅ use your real bucket name

        // ✅ Realtime Database
        $this->database = $this->firebase->createDatabase();
        
        // ✅ Cloud Messaging
        $this->messaging = $this->firebase->createMessaging();
        
        $this->storage  = $this->firebase->createStorage(); // ✅ initialize Firebase Storage
    }
    
    public function fetchFirstRecordByField(string $collectionPath, string $fieldName, string $fieldValue): ?array
    {
        // Get a reference to the collection (e.g., 'vehicles')
        $reference = $this->database->getReference($collectionPath);

        // Query the database: order by the field, filter for the specific value, and limit to 1 result.
        $snapshot = $reference
            ->orderByChild($fieldName)
            ->equalTo($fieldValue)
            ->limitToFirst(1)
            ->getSnapshot();

        if ($snapshot->hasChildren()) {
            $data = $snapshot->getValue();
            
            // The result of a query is an associative array where the key is the Firebase unique ID.
            $key = key($data); 
            
            return [
                'firebase_key' => $key,
                'data' => $data[$key]
            ];
        }

        return null;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function pushData($path, $data)
    {
        return $this->database->getReference($path)->push($data);
    }

     // ✅ New method to fetch data
    public function getData($path)
    {
        return $this->database->getReference($path)->getValue();
    }
    
    // ✅ Update existing data
    public function updateData($path, $data)
    {
        return $this->database->getReference($path)->update($data);
    }
    
    // ✅ Delete data
    public function deleteData($path)
    {
        return $this->database->getReference($path)->remove();
    }

    public function getFirestore()
    {
        return $this->firebase->createFirestore()->database();
    }

    public function getAuth()
    {
        return $this->firebase->createAuth();
    }
    
    public function getStorage()
    {
        return $this->storage;
    }

    
     // 🔧 Cloud Messaging
    public function getMessaging(): Messaging
    {
        return $this->messaging;
    }
    

    // 🚀 Upload file to Firebase Storage with local fallback
    public function uploadFile($file, $folder = 'drivers')
    {
        try {
            $bucket = $this->storage->getBucket();
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = $folder . '/' . uniqid('driver_') . '_' . time() . '.' . $extension;

            $bucket->upload(
                fopen($file->getRealPath(), 'r'),
                [
                    'name' => $fileName,
                    'predefinedAcl' => 'publicRead'
                ]
            );

            return sprintf(
                'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media',
                $bucket->name(),
                urlencode($fileName)
            );
        } catch (\Exception $e) {
            \Log::error('Firebase Storage upload error: ' . $e->getMessage());
            try {
                $destDir = public_path('uploads/' . $folder);
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0777, true);
                }
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $localName = uniqid('driver_') . '_' . time() . '.' . $extension;
                $file->move($destDir, $localName);
                return asset('uploads/' . $folder . '/' . $localName);
            } catch (\Exception $localErr) {
                \Log::error('Local storage fallback upload error: ' . $localErr->getMessage());
                return null;
            }
        }
    }

    // 🚀 Send FCM notification to a topic
    public function sendNotificationToTopic(string $topic, string $title, string $body)
    {
        $message = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
        ];

        return $this->messaging->send($message);
    }
}
