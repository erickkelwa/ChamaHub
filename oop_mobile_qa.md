# 15 OOP (Object-Oriented Programming) Questions & Answers in Mobile Development

---

### 1. What are the four main pillars of OOP, and how do they apply to mobile development?
* **Answer:**
  1. **Encapsulation:** Hiding internal state/logic and exposing only necessary methods (e.g., keeping mutable UI state private inside a `ViewModel` or `StatefulWidget`).
  2. **Abstraction:** Exposing high-level interfaces without implementation details (e.g., a `Repository` interface for fetching remote/local user data).
  3. **Inheritance:** Reusing code by deriving new classes from existing ones (e.g., extending `AppCompatActivity` in Android or `UIViewController` in iOS).
  4. **Polymorphism:** Performing a single action in different ways (e.g., custom rendering of different mobile UI widgets via overridden `draw()` or `build()` methods).

---

### 2. How is Encapsulation used in mobile app architecture?
* **Answer:**
  Encapsulation protects app state and prevents unintended modifications from UI components.
  * **Example:** In Android MVVM, a `ViewModel` keeps mutable state private (`_userList: MutableLiveData / MutableStateFlow`) and exposes only an immutable read-only view (`userList: LiveData / StateFlow`) to the Activity or Composable screen.

---

### 3. What is the difference between Abstract Classes and Interfaces/Protocols in mobile apps?
* **Answer:**
  * **Abstract Class:** Can have state (fields/properties) and default method implementations; a class can only inherit from one base class. Useful for shared base logic (e.g., `BaseActivity`, `BaseFragment`, or `BaseViewController`).
  * **Interface / Protocol:** Defines a contract without state (though some languages allow default implementations). A class can implement multiple interfaces (e.g., `OnClickListener`, `UITableViewDelegate`, `Equatable`).

---

### 4. What is Polymorphism, and how is it used in custom mobile UI components?
* **Answer:**
  Polymorphism allows objects of different classes to respond to the same interface or method call.
  * **Runtime Polymorphism (Method Overriding):** Subclasses override a base method (e.g., overriding `onDraw()` in an Android custom `View` or `draw(rect:)` in iOS `UIView`).
  * **Compile-time Polymorphism (Method Overloading):** Multiple methods with the same name but different parameter types (e.g., initializing a mobile alert dialog with title-only vs. title + custom button handlers).

---

### 5. Why is "Composition over Inheritance" preferred in modern mobile app development?
* **Answer:**
  Deep inheritance hierarchies (e.g., `BaseAuthNetworkFragment`) create rigid, tightly coupled code that breaks easily when requirements change.
  * **Composition** builds complex behavior by combining smaller, independent classes. For example, injecting a `LocationProvider` and `AnalyticsTracker` into a ViewModel/Presenter rather than inheriting from multiple base classes.

---

### 6. How does OOP handle memory management and avoid memory leaks in mobile (e.g., retain cycles / strong reference cycles)?
* **Answer:**
  In OOP, objects holding strong references to each other in a cycle (e.g., an asynchronous callback or delegate holding a strong reference to an Activity/UIViewController) prevent garbage collection or ARC deallocation.
  * **Solution:** Use **Weak References** (`WeakReference<T>` in Kotlin/Java, `weak var` in Swift) for listeners, delegates, or context references so the screen can be destroyed properly when dismissed.

---

### 7. What is the Singleton pattern, and what is its main caveat in mobile applications?
* **Answer:**
  * **Concept:** Ensures only one instance of a class exists across the entire app lifecycle (e.g., `DatabaseClient`, `NetworkManager`, `SharedPrefManager`).
  * **Caveat:** Singletons hold onto references for the lifetime of the application process. If a singleton holds an Activity/UI `Context`, it causes a massive memory leak. Always pass `ApplicationContext` instead of UI context to singletons.

---

### 8. What is the Factory Pattern and when do you use it in mobile development?
* **Answer:**
  The Factory pattern provides an interface/method to instantiate objects without specifying their exact concrete classes.
  * **Mobile Use Case:**
    * Creating different Push Notification handlers (`FCMNotificationHandler`, `APNSNotificationHandler`).
    * Instantiating `ViewModelProvider.Factory` in Android to inject dependencies into ViewModels.

---

### 9. What is the Observer Pattern, and how is it used in mobile reactive architectures?
* **Answer:**
  The Observer pattern defines a one-to-many dependency where an object (Subject) notifies multiple subscribers (Observers) of state changes.
  * **Mobile Use Case:**
    * Android: `LiveData`, `Flow`, `RxJava`.
    * iOS: `Combine` publishers, `NotificationCenter`, `@Published` properties.
    * Flutter: `ChangeNotifier`, `ValueNotifier`, `Streams`.

---

### 10. What is Dependency Injection (DI) and how does it relate to OOP principles?
* **Answer:**
  Dependency Injection fulfills the **Inversion of Control (IoC)** and **Dependency Inversion** principles by supplying dependencies from the outside rather than having an object instantiate them directly (`new Database()`).
  * **Benefits:** Decouples classes, simplifies unit testing with mock objects, and makes mobile apps modular.
  * **Common Mobile DI Frameworks:** Hilt / Dagger / Koin (Android), Swinject (iOS), GetIt / Injectable (Flutter).

---

### 11. How do OOP principles support Mobile Design Patterns like MVP and MVVM?
* **Answer:**
  * **Abstraction & Interfaces:** The View (UI) and Presenter/ViewModel communicate via interfaces/contracts, decoupling UI rendering from business logic.
  * **Encapsulation:** The Model encapsulates data/storage logic; the ViewModel encapsulates presentation state.
  * **Maintainability & Testability:** Allows unit-testing business logic on JVM/Host machines without running a full mobile emulator.

---

### 12. What are Data Classes / Models in mobile OOP, and why is immutability recommended?
* **Answer:**
  * **Data Classes** (Kotlin `data class`, Swift `struct`, Dart `freezed`/record) encapsulate domain data fields.
  * **Immutability (read-only properties):** Avoids race conditions between background threads (e.g., background API sync) and the main UI thread, ensuring predictable unidirectional data flow.

---

### 13. What is the Adapter Pattern and where is it commonly used in mobile development?
* **Answer:**
  The Adapter pattern acts as a bridge between two incompatible interfaces by converting data of one type into a format the consumer expects.
  * **Mobile Example:** Android `RecyclerView.Adapter` or `BaseAdapter`, which converts raw list items into visual `ViewHolder` UI components.

---

### 14. What are SOLID principles in OOP, and how do they benefit mobile apps?
* **Answer:**
  * **S (Single Responsibility):** A class should do one thing (e.g., an Activity only handles UI, not network requests).
  * **O (Open/Closed):** Classes are open for extension but closed for modification (e.g., plugins, custom view modifiers).
  * **L (Liskov Substitution):** Subtypes must be substitutable for their base types without breaking behavior.
  * **I (Interface Segregation):** Avoid bloated interfaces; break them into smaller ones (e.g., `AudioPlayerDelegate` vs `VideoPlayerDelegate`).
  * **D (Dependency Inversion):** Depend on abstractions (interfaces), not concrete classes (e.g., depending on `PaymentGateway` interface instead of `StripeService` directly).

---

### 15. How does OOP handle Asynchronous & Multithreading operations in mobile apps?
* **Answer:**
  Mobile apps run UI rendering on the **Main/UI thread** and heavy tasks (I/O, database, API calls) on **Background threads**.
  * OOP encapsulates background work in worker objects or repositories using concurrency abstractions (Kotlin Coroutines / Dispatchers, Swift Actors / GCD, Dart Isolates).
  * Callback interfaces, closures/lambdas, or reactive streams are used to communicate results back across thread boundaries to update encapsulated UI state.
