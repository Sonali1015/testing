package org.example;

import io.github.bonigarcia.wdm.WebDriverManager;
import org.junit.jupiter.api.*;
import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.support.ui.*;

import java.io.File;
import java.nio.file.Files;
import java.nio.file.Path;
import java.time.Duration;

@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class HotelSystemTests {

    private static WebDriver driver;
    private static final String BASE_URL = "http://localhost/testing-newso"; // change to your localhost path

    // Test users
    private static final String VALID_NAME = "Selenium Tester";
    private static final String VALID_PHONE = "0771234567";
    private static final String VALID_EMAIL = "seleniumuser@example.com";
    private static final String VALID_PASS = "Password123";

    @BeforeAll
    public static void setupClass() {
        WebDriverManager.chromedriver().setup();
        ChromeOptions options = new ChromeOptions();
        driver = new ChromeDriver(options);
        driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(5));
        new File("screenshots").mkdirs();
    }

    @AfterAll
    public static void teardownClass() {
        if (driver != null) driver.quit();
    }

    private WebElement waitFor(By locator, int sec) {
        return new WebDriverWait(driver, Duration.ofSeconds(sec))
                .until(ExpectedConditions.visibilityOfElementLocated(locator));
    }

    private void screenshot(String name) {
        try {
            File src = ((TakesScreenshot) driver).getScreenshotAs(OutputType.FILE);
            Path dest = Path.of("screenshots", name + "_" + System.currentTimeMillis() + ".png");
            Files.copy(src.toPath(), dest);
        } catch (Exception ignored) {
        }
    }

    // Helper for Thread.sleep
    private void sleep(int ms) {
        try {
            Thread.sleep(ms);
        } catch (InterruptedException ignored) {
        }
    }

    // =====================================
    // 1. REGISTER TEST CASES
    // =====================================
    @Test
    @Order(1)
    void testRegisterValid() {
        driver.get(BASE_URL + "/register.php");
        sleep(1000);
        waitFor(By.id("registrationFullName"), 5).sendKeys(VALID_NAME);
        driver.findElement(By.id("registrationPhoneNumber")).sendKeys(VALID_PHONE);
        driver.findElement(By.id("registrationEmail")).sendKeys("valid_" + System.currentTimeMillis() + "@example.com");
        driver.findElement(By.id("registrationPassword")).sendKeys(VALID_PASS);
        driver.findElement(By.id("registrationPassword2")).sendKeys(VALID_PASS);
        sleep(500);
        driver.findElement(By.name("registerSubmitBtn")).click();
        sleep(2000);
        screenshot("register_valid");
        Assertions.assertTrue(driver.getPageSource().contains("Registration successful"));
    }

    @Test
    @Order(2)
    void testRegisterInvalidEmail() {
        driver.get(BASE_URL + "/register.php");
        sleep(1000);
        waitFor(By.id("registrationFullName"), 5).sendKeys(VALID_NAME);
        driver.findElement(By.id("registrationPhoneNumber")).sendKeys(VALID_PHONE);
        driver.findElement(By.id("registrationEmail")).sendKeys("invalid-email");
        driver.findElement(By.id("registrationPassword")).sendKeys(VALID_PASS);
        driver.findElement(By.id("registrationPassword2")).sendKeys(VALID_PASS);
        sleep(500);
        driver.findElement(By.name("registerSubmitBtn")).click();
        sleep(2000);
        screenshot("register_invalid_email");
        Assertions.assertTrue(driver.getPageSource().contains("Invalid email"));
    }

    @Test
    @Order(3)
    void testRegisterEmptyFields() {
        driver.get(BASE_URL + "/register.php");
        sleep(1000);
        driver.findElement(By.name("registerSubmitBtn")).click();
        sleep(2000);
        screenshot("register_empty");
        Assertions.assertTrue(driver.getPageSource().contains("This field is required"));
    }

    // =====================================
    // 2. LOGIN TEST CASES
    // =====================================
    @Test
    @Order(4)
    void testLoginValid() {
        driver.get(BASE_URL + "/sign-in.php");
        sleep(1000);
        waitFor(By.id("loginEmail"), 5).sendKeys(VALID_EMAIL);
        driver.findElement(By.id("loginPassword")).sendKeys(VALID_PASS);
        sleep(500);
        driver.findElement(By.name("loginSubmitBtn")).click();
        sleep(2000);
        screenshot("login_valid");
        Assertions.assertTrue(driver.getPageSource().contains("Sign Out"));
    }

    @Test
    @Order(5)
    void testLoginInvalidPassword() {
        driver.get(BASE_URL + "/sign-in.php");
        sleep(1000);
        waitFor(By.id("loginEmail"), 5).sendKeys(VALID_EMAIL);
        driver.findElement(By.id("loginPassword")).sendKeys("WrongPass123");
        sleep(500);
        driver.findElement(By.name("loginSubmitBtn")).click();
        sleep(2000);
        screenshot("login_invalid_pass");
        Assertions.assertTrue(driver.getPageSource().contains("Invalid credentials"));
    }

    @Test
    @Order(6)
    void testLoginEmptyFields() {
        driver.get(BASE_URL + "/sign-in.php");
        sleep(1000);
        driver.findElement(By.name("loginSubmitBtn")).click();
        sleep(2000);
        screenshot("login_empty");
        Assertions.assertTrue(driver.getPageSource().contains("This field is required"));
    }

    // =====================================
    // 3. BOOKING TEST CASES
    // =====================================
    @Test
    @Order(7)
    void testBookingValid() {
        driver.get(BASE_URL + "/index.php");

        // Click the booking button
        WebElement bookBtn = new WebDriverWait(driver, Duration.ofSeconds(8))
                .until(ExpectedConditions.elementToBeClickable(By.cssSelector(".room-card .btn-primary")));
        bookBtn.click();

        // Fill the form
        WebElement startDate = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.elementToBeClickable(By.name("startDate")));
        startDate.click();
        startDate.clear();
        startDate.sendKeys("2025-10-01");

        WebElement endDate = driver.findElement(By.name("endDate"));
        endDate.click();
        endDate.clear();
        endDate.sendKeys("2025-10-05");

        driver.findElement(By.name("roomType")).sendKeys("Double");
        driver.findElement(By.name("roomRequirement")).sendKeys("non smoking");
        driver.findElement(By.name("adults")).sendKeys("2");
        driver.findElement(By.name("children")).sendKeys("1");
        driver.findElement(By.name("specialRequests")).sendKeys("Need sea view");

        // Click Next button
        WebElement nextBtn = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.elementToBeClickable(By.id("rsvnNextBtn")));
        nextBtn.click();

        // Wait for success message (use XPath if CSS class not standard)
        WebElement message = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.visibilityOfElementLocated(
                        By.xpath("//*[contains(text(),'Booking confirmed')]")));

        screenshot("booking_valid");
        Assertions.assertTrue(message.getText().contains("Booking confirmed"));
    }

    @Test
    @Order(8)
    void testBookingInvalidDates() {
        driver.get(BASE_URL + "/index.php");

        WebElement bookBtn = new WebDriverWait(driver, Duration.ofSeconds(8))
                .until(ExpectedConditions.elementToBeClickable(By.cssSelector(".room-card .btn-primary")));
        bookBtn.click();

        WebElement startDate = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.elementToBeClickable(By.name("startDate")));
        startDate.click();
        startDate.clear();
        startDate.sendKeys("2025-10-05");

        WebElement endDate = driver.findElement(By.name("endDate"));
        endDate.click();
        endDate.clear();
        endDate.sendKeys("2025-10-01"); // invalid

        WebElement nextBtn = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.elementToBeClickable(By.id("rsvnNextBtn")));
        nextBtn.click();

        WebElement message = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.visibilityOfElementLocated(
                        By.xpath("//*[contains(text(),'End date must be after start date')]")));

        screenshot("booking_invalid_dates");
        Assertions.assertTrue(message.getText().contains("End date must be after start date"));
    }

    @Test
    @Order(9)
    void testBookingEmptyFields() {
        driver.get(BASE_URL + "/index.php");

        WebElement bookBtn = new WebDriverWait(driver, Duration.ofSeconds(8))
                .until(ExpectedConditions.elementToBeClickable(By.cssSelector(".room-card .btn-primary")));
        bookBtn.click();

        WebElement nextBtn = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.elementToBeClickable(By.id("rsvnNextBtn")));
        nextBtn.click();

        WebElement message = new WebDriverWait(driver, Duration.ofSeconds(5))
                .until(ExpectedConditions.visibilityOfElementLocated(
                        By.xpath("//*[contains(text(),'This field is required')]")));

        screenshot("booking_empty");
        Assertions.assertTrue(message.getText().contains("This field is required"));
    }

    // =====================================
    // 4. VIEW BOOKINGS TEST CASES
    // =====================================
    @Test
    @Order(10)
    void testViewBookingsNormal() {
        driver.get(BASE_URL + "/index.php");
        sleep(1000);
        WebElement myBookingsBtn = waitFor(By.cssSelector(".my-reservations"), 5);
        myBookingsBtn.click();
        sleep(1000);
        WebElement resDiv = waitFor(By.id("my-reservations-div"), 5);
        screenshot("view_bookings_normal");
        Assertions.assertTrue(resDiv.getText().contains("Reservations") || resDiv.getText().contains("No reservations"));
    }

    @Test
    @Order(11)
    void testViewBookingsNoReservations() {
        driver.get(BASE_URL + "/index.php");
        sleep(1000);
        WebElement myBookingsBtn = waitFor(By.cssSelector(".my-reservations"), 5);
        myBookingsBtn.click();
        sleep(1000);
        WebElement resDiv = waitFor(By.id("my-reservations-div"), 5);
        screenshot("view_bookings_none");
        Assertions.assertTrue(resDiv.getText().contains("No reservations"));
    }



}