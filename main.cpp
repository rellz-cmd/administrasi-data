/******************************************************************************

                              Online C++ Compiler.
               Code, Compile, Run and Debug C++ program online.
Write your code in this editor and press "Run" button to compile and execute it.

*******************************************************************************/
#include <iostream>
#include <vector>
#include <string>
#include <unordered_map>
#include <iomanip>

using namespace std;

struct Student {
    int id;
    string name;
    int age;
    string grade;
};

struct Subject {
    string name;
    string teacher;
};

struct Schedule {
    string day;
    string time;
    int classId; // Bisa dihubungkan ke kelas/tingkatan tertentu
    string subjectName;
};

class SchoolManagement {
private:
    vector<Student> students;
    vector<Subject> subjects;
    vector<Schedule> schedules;
    unordered_map<int, vector<pair<string, bool>>> attendance; // student ID -> (date, isPresent)
    int nextStudentId = 1;

public:
    // Siswa
    void addStudent() {
        Student s;
        s.id = nextStudentId++;
        cout << "Nama siswa: "; getline(cin >> ws, s.name);
        cout << "Usia: "; cin >> s.age; cin.ignore();
        cout << "Kelas (misal: 10A): "; getline(cin, s.grade);
        students.push_back(s);
        cout << "Siswa berhasil ditambahkan dengan ID: " << s.id << endl;
    }

    void listStudents() {
        if (students.empty()) {
            cout << "Belum ada data siswa." << endl;
            return;
        }
        cout << "\n--- Daftar Siswa ---" << endl;
        cout << left << setw(5) << "ID" << setw(20) << "Nama" << setw(5) << "Usia" << setw(8) << "Kelas" << endl;
        cout << setfill('-') << setw(38) << "" << setfill(' ') << endl;
        for (const auto& s : students) {
            cout << left << setw(5) << s.id << setw(20) << s.name << setw(5) << s.age << setw(8) << s.grade << endl;
        }
    }

    // Mata Pelajaran
    void addSubject() {
        Subject sub;
        cout << "Nama mata pelajaran: "; getline(cin >> ws, sub.name);
        cout << "Nama guru pengampu: "; getline(cin, sub.teacher);
        subjects.push_back(sub);
        cout << "Mata pelajaran berhasil ditambahkan." << endl;
    }

    void listSubjects() {
        if (subjects.empty()) {
            cout << "Belum ada data mata pelajaran." << endl;
            return;
        }
        cout << "\n--- Daftar Mata Pelajaran ---" << endl;
        cout << left << setw(20) << "Nama Pelajaran" << setw(20) << "Guru Pengampu" << endl;
        cout << setfill('-') << setw(40) << "" << setfill(' ') << endl;
        for (const auto& sub : subjects) {
            cout << left << setw(20) << sub.name << setw(20) << sub.teacher << endl;
        }
    }

    // Jadwal Pelajaran
    void addSchedule() {
        Schedule sch;
        cout << "Hari (misal: Senin): "; getline(cin >> ws, sch.day);
        cout << "Waktu (misal: 07:00-08:00): "; getline(cin, sch.time);
        cout << "ID Kelas: "; cin >> sch.classId; cin.ignore();
        cout << "Nama Mata Pelajaran: "; getline(cin, sch.subjectName);
        schedules.push_back(sch);
        cout << "Jadwal berhasil ditambahkan." << endl;
    }

    void listSchedules() {
        if (schedules.empty()) {
            cout << "Belum ada jadwal pelajaran." << endl;
            return;
        }
        cout << "\n--- Jadwal Pelajaran ---" << endl;
        cout << left << setw(10) << "Hari" << setw(15) << "Waktu" << setw(10) << "Kelas ID" << setw(20) << "Mata Pelajaran" << endl;
        cout << setfill('-') << setw(55) << "" << setfill(' ') << endl;
        for (const auto& sch : schedules) {
            cout << left << setw(10) << sch.day << setw(15) << sch.time << setw(10) << sch.classId << setw(20) << sch.subjectName << endl;
        }
    }

    // Kehadiran
    void markAttendance() {
        int studentId;
        cout << "Masukkan ID Siswa: "; cin >> studentId; cin.ignore();

        bool found = false;
        for (const auto& s : students) {
            if (s.id == studentId) {
                found = true;
                break;
            }
        }

        if (!found) {
            cout << "Siswa dengan ID " << studentId << " tidak ditemukan." << endl;
            return;
        }

        string date;
        cout << "Tanggal kehadiran (YYYY-MM-DD): "; getline(cin, date);
        char hadir;
        cout << "Hadir? (y/n): "; cin >> hadir; cin.ignore();
        attendance[studentId].push_back({date, (hadir == 'y' || hadir == 'Y')});
        cout << "Kehadiran untuk siswa ID " << studentId << " pada tanggal " << date << " berhasil dicatat." << endl;
    }

    void viewAttendance(int studentId) {
        bool found = false;
        string studentName;
        for (const auto& s : students) {
            if (s.id == studentId) {
                found = true;
                studentName = s.name;
                break;
            }
        }

        if (!found) {
            cout << "Siswa dengan ID " << studentId << " tidak ditemukan." << endl;
            return;
        }

        cout << "\n--- Kehadiran Siswa: " << studentName << " (ID: " << studentId << ") ---" << endl;
        if (attendance.count(studentId) > 0) {
            cout << left << setw(15) << "Tanggal" << setw(10) << "Hadir" << endl;
            cout << setfill('-') << setw(25) << "" << setfill(' ') << endl;
            for (const auto& att : attendance[studentId]) {
                cout << left << setw(15) << att.first << setw(10) << (att.second ? "Ya" : "Tidak") << endl;
            }
        } else {
            cout << "Belum ada catatan kehadiran untuk siswa ini." << endl;
        }
    }

    void run() {
        int choice;
        do {
            cout << "\n=== Aplikasi Manajemen Sekolah ===" << endl;
            cout << "1. Kelola Siswa" << endl;
            cout << "2. Kelola Mata Pelajaran" << endl;
            cout << "3. Kelola Jadwal Pelajaran" << endl;
            cout << "4. Kelola Kehadiran" << endl;
            cout << "0. Keluar" << endl;
            cout << "Pilih menu: ";
            cin >> choice;
            cin.ignore();

            switch (choice) {
                case 1:
                    manageStudents();
                    break;
                case 2:
                    manageSubjects();
                    break;
                case 3:
                    manageSchedules();
                    break;
                case 4:
                    manageAttendance();
                    break;
                case 0:
                    cout << "Terima kasih telah menggunakan aplikasi ini." << endl;
                    break;
                default:
                    cout << "Pilihan tidak valid. Silakan coba lagi." << endl;
            }
        } while (choice != 0);
    }

private:
    void manageStudents() {
        int choice;
        do {
            cout << "\n--- Kelola Siswa ---" << endl;
            cout << "1. Tambah Siswa" << endl;
            cout << "2. Daftar Siswa" << endl;
            cout << "0. Kembali" << endl;
            cout << "Pilih opsi: ";
            cin >> choice;
            cin.ignore();

            switch (choice) {
                case 1:
                    addStudent();
                    break;
                case 2:
                    listStudents();
                    break;
                case 0:
                    break;
                default:
                    cout << "Pilihan tidak valid. Silakan coba lagi." << endl;
            }
        } while (choice != 0);
    }

    void manageSubjects() {
        int choice;
        do {
            cout << "\n--- Kelola Mata Pelajaran ---" << endl;
            cout << "1. Tambah Mata Pelajaran" << endl;
            cout << "2. Daftar Mata Pelajaran" << endl;
            cout << "0. Kembali" << endl;
            cout << "Pilih opsi: ";
            cin >> choice;
            cin.ignore();

            switch (choice) {
                case 1:
                    addSubject();
                    break;
                case 2:
                    listSubjects();
                    break;
                case 0:
                    break;
                default:
                    cout << "Pilihan tidak valid. Silakan coba lagi." << endl;
            }
        } while (choice != 0);
    }

    void manageSchedules() {
        int choice;
        do {
            cout << "\n--- Kelola Jadwal Pelajaran ---" << endl;
            cout << "1. Tambah Jadwal" << endl;
            cout << "2. Daftar Jadwal" << endl;
            cout << "0. Kembali" << endl;
            cout << "Pilih opsi: ";
            cin >> choice;
            cin.ignore();

            switch (choice) {
                case 1:
                    addSchedule();
                    break;
                case 2:
                    listSchedules();
                    break;
                case 0:
                    break;
                default:
                    cout << "Pilihan tidak valid. Silakan coba lagi." << endl;
            }
        } while (choice != 0);
    }

    void manageAttendance() {
        int choice;
        do {
            cout << "\n--- Kelola Kehadiran ---" << endl;
            cout << "1. Catat Kehadiran" << endl;
            cout << "2. Lihat Kehadiran Siswa" << endl;
            cout << "0. Kembali" << endl;
            cout << "Pilih opsi: ";
            cin >> choice;
            cin.ignore();

            switch (choice) {
                case 1:
                    markAttendance();
                    break;
                case 2: {
                    int studentId;
                    cout << "Masukkan ID Siswa untuk melihat kehadiran: ";
                    cin >> studentId;
                    cin.ignore();
                    viewAttendance(studentId);
                    break;
                }
                case 0:
                    break;
                default:
                    cout << "Pilihan tidak valid. Silakan coba lagi." << endl;
            }
        } while (choice != 0);
    }
};

int main() {
    SchoolManagement app;
    app.run();
    return 0;
}